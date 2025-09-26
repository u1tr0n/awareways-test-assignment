<?php

namespace App\Dto\Request;

use Symfony\Component\Uid\UuidV7;

final readonly class QuizQuestionRemoveData
{
    public function __construct(
        public UuidV7 $questionId,
        public UuidV7 $quizId,
    ) {}
}
