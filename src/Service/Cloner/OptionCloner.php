<?php

namespace App\Service\Cloner;

use App\Entity\Option;
use Doctrine\ORM\EntityManagerInterface;

final readonly class OptionCloner implements OptionClonerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function clone(Option $option): Option
    {
        $newOption = new Option()->setTitle($option->getTitle())->setCorrect($option->isCorrect())->setRef($option);

        $option->addReferral($newOption);

        $this->em->persist($newOption);

        return $newOption;
    }
}
