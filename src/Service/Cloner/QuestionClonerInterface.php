<?php

namespace App\Service\Cloner;

use App\Entity\Option;
use App\Entity\Question;
use Symfony\Component\Uid\UuidV7;

interface QuestionClonerInterface
{
    /**
     * @param Question $question
     * @param array<array-key, UuidV7> $excludeOptions
     * @param array<non-empty-string, Option> $replaceOptions
     * @return Question
     */
    public function clone(Question $question, array $excludeOptions = [], array $replaceOptions = [], bool $persist = true): Question;
}
