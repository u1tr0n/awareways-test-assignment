<?php

namespace App\Service;

use App\Dto\Form\QuestionDto;
use App\Dto\QuizVersionServiceResult;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizVersion;
use App\Enum\QuizStatus;
use App\Exception\QuestionNotFoundException;
use App\Exception\QuizNotFoundException;
use App\Repository\QuestionRepository;
use App\Repository\QuizQuestionRepository;
use App\Repository\QuizVersionRepository;
use App\SemVer\SemVer;
use App\Service\Cloner\QuestionClonerInterface;
use App\Service\Cloner\QuizClonerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\UuidV7;

final readonly class QuizService implements QuizServiceInterface
{
    public function __construct(
        private QuizVersionRepository $quizVersionRepository,
        private QuizQuestionRepository $quizQuestionRepository,
        private QuizClonerInterface $quizCloner,
        private EntityManagerInterface $em,
        private QuestionClonerInterface $questionCloner,
        private QuestionRepository $questionRepository,
        private BitmaskVersionResolverInterface $bitmaskResolver,
    ) {}

    /**
     * @throws QuizNotFoundException
     */
    public function load(UuidV7 $id, ?string $version = null): QuizVersion
    {
        $quiz = $this->quizVersionRepository->load($id, $version);

        if (null === $quiz) {
            throw new QuizNotFoundException('Quiz not found');
        }

        return $quiz;
    }

    /**
     * @throws QuizNotFoundException
     */
    public function get(UuidV7 $id): QuizVersion
    {
        $quiz = $this->quizVersionRepository->findOneBy(['id' => $id]);

        if (null === $quiz) {
            throw new QuizNotFoundException("Quiz with ID=\"{$id}\" not found");
        }

        return $quiz;
    }

    public function createQuiz(Quiz $quiz): QuizVersion
    {
        $this->em->persist($quiz);
        $quizVersion = new QuizVersion()
            ->setQuiz($quiz)
            ->setStatus(QuizStatus::DRAFT)
            ->setVersion((string)new SemVer())
        ;

        $this->em->persist($quizVersion);
        $this->em->flush();

        return $quizVersion;
    }

    /**
     * @throws QuizNotFoundException
     */
    public function addQuestion(UuidV7 $quizVersionId, Question|QuestionDto $question, ?bool $isDraft = null): QuizVersionServiceResult
    {
        $quizVersion = $this->get($quizVersionId);

        if (is_null($isDraft)) {
            $isDraft = $quizVersion->getStatus()->isDraft();
        }

        if (false === $isDraft) {
            $quizVersion = $this->quizCloner->clone($quizVersion);
        }

        if ($question instanceof QuestionDto) {
            $entity = $this->questionRepository->find($question->id);
            if (null === $entity) {
                $question = $question->fillEntity(new Question());
            } else {
                $question = $question->fillEntity($entity);
            }

            $this->em->persist($question);
        }

        $quizQuestion = new QuizQuestion()
            ->setQuestion($question)
            ->setQuizVersion($quizVersion)
            ->setSortPosition($this->quizQuestionRepository->getNextSortPosition($quizVersionId))
        ;
        $this->em->persist($quizQuestion);

        $this->em->flush();

        return new QuizVersionServiceResult($quizVersion, 0b100);
    }

    /**
     * @throws QuizNotFoundException
     * @throws QuestionNotFoundException
     */
    public function removeQuestion(UuidV7 $quizVersionId, UuidV7 $questionId, ?bool $isDraft = null): QuizVersionServiceResult
    {
        $quizVersion = $this->get($quizVersionId);

        if (is_null($isDraft)) {
            $isDraft = $quizVersion->getStatus()->isDraft();
        }

        if (false === $isDraft) {
            $quizVersion = $this->quizCloner->clone($quizVersion, excludeQuestions: [$questionId]);
        } else {
            $quizQuestion = $this->getQuestionById($questionId, $quizVersion->getQuizQuestions());

            $quizVersion->removeQuizQuestion($quizQuestion);
            $this->em->remove($quizQuestion);
        }
        $this->em->persist($quizVersion);

        $this->em->flush();

        return new QuizVersionServiceResult($quizVersion, 0b100);
    }

    /**
     * @throws QuizNotFoundException
     * @throws QuestionNotFoundException
     */
    public function updateQuestion(
        UuidV7 $quizVersionId,
        UuidV7 $targetQuestionId,
        Question|QuestionDto $question,
        ?bool $isDraft = null,
    ): QuizVersionServiceResult {
        $quizVersion = $this->get($quizVersionId);

        if (is_null($isDraft)) {
            $isDraft = $quizVersion->getStatus()->isDraft();
        }

        if (false === $isDraft) {
            if ($question instanceof QuestionDto) {
                $newQuestion = $this->getQuestionEntityFromDto($question, clone: true);
            } else {
                $newQuestion = $this->questionCloner->clone($question);
            }

            $quizVersion = $this->quizCloner->clone($quizVersion, replaceQuestions: [$targetQuestionId->toRfc4122() => $newQuestion]);

            $this->em->persist($quizVersion);
        } else {
            $refCount = $this->quizQuestionRepository->getRefCount($targetQuestionId);

            if (1 === $refCount) {
                $targetQuizQuestion = $this->getQuestionById($targetQuestionId, $quizVersion->getQuizQuestions());
                $targetQuestion = $targetQuizQuestion->getQuestion();
                if ($question instanceof QuestionDto) {
                    $question = $this->getQuestionEntityFromDto($question, clone: false);
                }
                $targetQuestion
                    ->setType($question->getType())
                    ->setMeta($question->getMeta())
                    ->setTitle($question->getTitle())
                    ->setDescription($question->getDescription())
                    ->setOptions($question->getOptions())
                    ->setCategory($question->getCategory())
                    ->setTags($question->getTags()->toArray())
                ;

                $this->em->persist($targetQuestion);
                $targetQuizQuestion->setQuestion($targetQuestion);
                $this->em->persist($targetQuizQuestion);
            } else {
                $quizQuestion = $this->getQuestionById($targetQuestionId, $quizVersion->getQuizQuestions());

                $questionId = $question instanceof QuestionDto ? $question->id : $question->getId();

                if (0 === $this->quizQuestionRepository->getRefCount($questionId)) {
                    if ($question instanceof QuestionDto) {
                        $question = $this->getQuestionEntityFromDto($question, clone: false);
                    }
                    $quizQuestion->setQuestion($question);
                    $this->em->persist($quizQuestion);
                } else {
                    $sortPosition = $quizQuestion->getSortPosition();

                    $this->em->remove($quizQuestion);

                    if ($question instanceof QuestionDto) {
                        $newQuestion = $this->getQuestionEntityFromDto($question, clone: true);
                    } else {
                        $newQuestion = $this->questionCloner->clone($question);
                        $newQuestion
                            ->setType($question->getType())
                            ->setMeta($question->getMeta())
                            ->setTitle($question->getTitle())
                            ->setDescription($question->getDescription())
                            ->setOptions($question->getOptions())
                        ;
                    }
                    $this->em->persist($newQuestion);

                    $newQuizQuestion = new QuizQuestion()
                        ->setQuestion($newQuestion)
                        ->setQuizVersion($quizVersion)
                        ->setSortPosition($sortPosition)
                    ;

                    $this->em->persist($newQuizQuestion);
                }
            }
        }

        $this->em->flush();

        return new QuizVersionServiceResult($quizVersion, 0b100);
    }

    /**
     * @throws QuizNotFoundException
     * @throws QuestionNotFoundException
     */
    public function swapQuestion(UuidV7 $quizVersionId, UuidV7 $firstQuestionId, UuidV7 $secondQuestionId, ?bool $isDraft = null): QuizVersionServiceResult
    {
        $quizVersion = $this->get($quizVersionId);

        if (is_null($isDraft)) {
            $isDraft = $quizVersion->getStatus()->isDraft();
        }

        if (false === $isDraft) {
            $quizVersion = $this->quizCloner->clone($quizVersion);
        }

        $quizQuestions = $quizVersion->getQuizQuestions();

        $firstOption = $this->getQuestionById($firstQuestionId, $quizQuestions);
        $secondOption = $this->getQuestionById($secondQuestionId, $quizQuestions);

        $firstOptionPosition = $firstOption->getSortPosition();
        $firstOption->setSortPosition($secondOption->getSortPosition());
        $secondOption->setSortPosition($firstOptionPosition);

        $this->em->persist($firstOption);
        $this->em->persist($secondOption);

        $this->em->flush();

        return new QuizVersionServiceResult($quizVersion, 0b001);
    }

    /**
     * {@inheritDoc}
     */
    public function changeQuizStatus(UuidV7 $quizVersionId, ?bool $isDraft = null): QuizVersion
    {
        $quizVersion = $this->get($quizVersionId);

        if (is_null($isDraft)) {
            $isDraft = $quizVersion->getStatus()->isDraft();
        }

        if (false === $isDraft) {
            $quizVersion->setStatus(QuizStatus::PUBLISHED);
        } else {
            $quizVersion->setStatus(QuizStatus::DRAFT);
        }

        $this->em->persist($quizVersion);
        $this->em->flush();

        return $quizVersion;
    }

    public function resolveVersion(QuizVersion $quizVersion, int $newBitmask): void
    {
        $currentVersion = $quizVersion->getVersion();
        $result = $this->bitmaskResolver->resolve(
            version: $currentVersion,
            currentBitmask: $quizVersion->getCurrentBitmask(),
            newBitmask: $newBitmask
        );

        if ($result['version'] !== $currentVersion) {
            $quizVersion->setVersion($result['version'])->setCurrentBitmask($result['bitmask']);
            $this->em->persist($quizVersion);
            $this->em->flush();
        }
    }

    /**
     * @param Collection<int, QuizQuestion> $haystack
     *
     * @throws QuestionNotFoundException
     */
    private function getQuestionById(UuidV7 $id, Collection $haystack): QuizQuestion
    {
        foreach ($haystack as $quizQuestion) {
            if ($quizQuestion->getQuestion()->getId()->equals($id)) {
                return $quizQuestion;
            }
        }

        throw new QuestionNotFoundException("Question with ID=\"{$id}\" not found");
    }

    /**
     * @throws QuestionNotFoundException
     */
    private function getQuestionEntityFromDto(QuestionDto $dto, bool $clone): Question
    {
        $question = $this->questionRepository->find($dto->id);
        if (null === $question) {
            throw new QuestionNotFoundException('Question not found');
        }
        if (true === $clone) {
            $question = $this->questionCloner->clone($question);
        }
        $dto->fillEntity($question);

        return $question;
    }
}
