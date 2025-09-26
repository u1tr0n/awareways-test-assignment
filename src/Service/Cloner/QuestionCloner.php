<?php

namespace App\Service\Cloner;

use App\Entity\Question;
use App\Entity\QuestionOption;
use App\Repository\OptionRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class QuestionCloner implements QuestionClonerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private OptionRepository $optionRepository,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function clone(Question $question, array $excludeOptions = [], array $replaceOptions = [], bool $persist = true): Question
    {
        $newQuestion = new Question()
            ->setMeta($question->getMeta())
            ->setTitle($question->getTitle())
            ->setDescription($question->getDescription())
            ->setType($question->getType())
            ->setCategory($question->getCategory())
            ->setRef($question->getRef())
        ;

        $question->addReferral($newQuestion);

        foreach ($question->getTags() as $tag) {
            $newQuestion->addTag($tag);
        }

        $options = $this->optionRepository->getQuestionOptions($question->getId());
        $position = 0;
        foreach ($options as $option) {
            if (!in_array($option->getId(), $excludeOptions)) {
                $newOption = new QuestionOption()
                    ->setQuestion($newQuestion)
                    ->setSortPosition(++$position)
                ;
                $strOptionId = $option->getId()->toRfc4122();
                if (array_key_exists($strOptionId, $replaceOptions)) {
                    $this->em->persist($replaceOptions[$strOptionId]);
                    $newOption->setOption($replaceOptions[$strOptionId]);
                } else {
                    $newOption->setOption($option);
                }
                $this->em->persist($newOption);
                $newQuestion->addOption($newOption);
            }
        }

        if (true === $persist) {
            $this->em->persist($newQuestion);
        }

        return $newQuestion;
    }
}
