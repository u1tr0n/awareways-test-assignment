<?php

namespace App\Service;

use App\Dto\QuestionServiceResult;
use App\Entity\Option;
use App\Entity\Question;
use App\Entity\QuestionOption;
use App\Exception\OptionNotFoundException;
use App\Exception\QuestionNotFoundException;
use App\Repository\QuestionOptionRepository;
use App\Repository\QuestionRepository;
use App\Repository\QuizQuestionRepository;
use App\Service\Cloner\QuestionClonerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\UuidV7;

final readonly class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        private QuestionRepository $questionRepository,
        private QuizQuestionRepository $quizQuestionRepository,
        private QuestionOptionRepository $questionOptionRepository,
        private QuestionClonerInterface $questionCloner,
        private EntityManagerInterface $em,
    ) {}

    /**
     * @throws QuestionNotFoundException
     */
    public function get(UuidV7 $id): Question
    {
        $question = $this->questionRepository->findOneBy(['id' => $id]);

        if (null === $question) {
            throw new QuestionNotFoundException('Question not found');
        }

        return $question;
    }

    /**
     * @throws QuestionNotFoundException
     */
    public function addOption(UuidV7 $questionId, Option $option, bool $isDraft = false): QuestionServiceResult
    {
        $question = $this->get($questionId);

        if (false === $isDraft) {
            $question = $this->questionCloner->clone($question);
        }

        $this->em->persist($question);
        $this->em->persist($option);

        $questionOption = new QuestionOption()
            ->setOption($option)
            ->setQuestion($question)
            ->setSortPosition($this->questionOptionRepository->getNextSortPosition($questionId))
        ;
        $this->em->persist($questionOption);

        $this->em->flush();

        return new QuestionServiceResult($question, 0b010);
    }

    /**
     * @throws QuestionNotFoundException
     * @throws OptionNotFoundException
     */
    public function removeOption(UuidV7 $questionId, UuidV7 $optionId, bool $isDraft = false): QuestionServiceResult
    {
        $question = $this->get($questionId);

        if (false === $isDraft) {
            $question = $this->questionCloner->clone($question, excludeOptions: [$optionId]);
        } else {
            $option = $this->getOptionById($optionId, $question->getOptions());
            $question->removeOption($option);
        }
        $this->em->persist($question);

        $this->em->flush();

        return new QuestionServiceResult($question, 0b010);
    }

    /**
     * @throws QuestionNotFoundException
     * @throws OptionNotFoundException
     */
    public function updateOption(UuidV7 $questionId, UuidV7 $targetOptionId, Option $option, bool $isDraft = false): QuestionServiceResult
    {
        $question = $this->get($questionId);

        $optionRefCount = $this->questionOptionRepository->getRefCount($targetOptionId);
        $questionRefCount = $this->quizQuestionRepository->getRefCount($questionId);

        if (false === $isDraft || 1 !== $optionRefCount || 1 !== $questionRefCount) {
            $question = $this->questionCloner->clone($question, replaceOptions: [$targetOptionId->toRfc4122() => $option]);
            $this->em->persist($question);
        } else {
            $targetQuestionOption = $this->getOptionById($targetOptionId, $question->getOptions());
            $targetOption = $targetQuestionOption->getOption();
            $targetOption->setTitle($option->getTitle())->setCorrect($option->isCorrect());

            $targetQuestionOption->setOption($targetOption);
            $this->em->persist($targetQuestionOption);

            $this->em->persist($targetOption);
        }

        $this->em->flush();

        return new QuestionServiceResult($question, 0b010);
    }

    /**
     * @throws QuestionNotFoundException
     * @throws OptionNotFoundException
     */
    public function swapOptions(UuidV7 $questionId, UuidV7 $firstOptionId, UuidV7 $secondOptionId, bool $isDraft = false): QuestionServiceResult
    {
        $question = $this->get($questionId);

        if (false === $isDraft) {
            $question = $this->questionCloner->clone($question);
        }

        $firstOption = $this->getOptionById($firstOptionId, $question->getOptions());
        $secondOption = $this->getOptionById($secondOptionId, $question->getOptions());

        $firstOptionPosition = $firstOption->getSortPosition();
        $firstOption->setSortPosition($secondOption->getSortPosition());
        $secondOption->setSortPosition($firstOptionPosition);

        $this->em->persist($firstOption);
        $this->em->persist($secondOption);

        $this->em->flush();

        return new QuestionServiceResult($question, 0b001);
    }

    /**
     * @param Collection<int, QuestionOption> $haystack
     *
     * @throws OptionNotFoundException
     */
    public function getOptionById(UuidV7 $id, Collection $haystack): QuestionOption
    {
        foreach ($haystack as $questionOption) {
            if ($questionOption->getOption()->getId()->equals($id)) {
                return $questionOption;
            }
        }

        throw new OptionNotFoundException("Option with ID=\"{$id}\" not found");
    }
}
