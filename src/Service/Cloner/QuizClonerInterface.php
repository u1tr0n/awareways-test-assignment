<?php

namespace App\Service\Cloner;

use App\Entity\Question;
use App\Entity\QuizVersion;
use Symfony\Component\Uid\UuidV7;

interface QuizClonerInterface
{
    /**
     * @param QuizVersion $quizVersion
     * @param array<array-key, UuidV7> $excludeQuestions
     * @param array<non-empty-string, Question> $replaceQuestions
     * @return QuizVersion
     */
    public function clone(QuizVersion $quizVersion, array $excludeQuestions = [], array $replaceQuestions = []): QuizVersion;
}
