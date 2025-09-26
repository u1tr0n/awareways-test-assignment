<?php

namespace App\Service\Cloner;

use App\Entity\QuizQuestion;
use App\Entity\QuizVersion;
use App\Enum\QuizStatus;
use App\Repository\QuizVersionRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class QuizCloner implements QuizClonerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private QuizVersionRepository $quizVersionRepository,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function clone(QuizVersion $quizVersion, array $excludeQuestions = [], array $replaceQuestions = []): QuizVersion
    {
        $nextVer = $this->quizVersionRepository->getMaxVersion($quizVersion->getQuiz()->getId())->bumpPatch();

        $newQuiz = new QuizVersion()
            ->setQuiz($quizVersion->getQuiz())
            ->setVersion((string)$nextVer)
            ->setStatus(QuizStatus::DRAFT)
            ->setRef($quizVersion)
        ;

        $quizVersion->addReferral($newQuiz);

        $quizQuestions = $quizVersion->getQuizQuestions();
        foreach ($quizQuestions as $quizQuestion) {
            if (!in_array($quizQuestion->getQuestion()->getId(), $excludeQuestions)) {
                $newQuizQuestion = new QuizQuestion()
                    ->setSortPosition($quizQuestion->getSortPosition())
                    ->setQuizVersion($newQuiz)
                ;
                $strQuestionId = $quizQuestion->getQuestion()->getId()->toRfc4122();
                if (array_key_exists($strQuestionId, $replaceQuestions)) {
                    $this->em->persist($replaceQuestions[$strQuestionId]);
                    $newQuizQuestion->setQuestion($replaceQuestions[$strQuestionId]);
                } else {
                    $newQuizQuestion->setQuestion($quizQuestion->getQuestion());
                }
                $this->em->persist($newQuizQuestion);
                $newQuiz->addQuizQuestion($newQuizQuestion);
            }
        }

        $this->em->persist($newQuiz);

        return $newQuiz;
    }
}
