<?php

namespace App\Dto;

use App\Entity\QuizVersion;

final readonly class QuizVersionServiceResult
{
    public function __construct(
        public QuizVersion $quizVersion,
        public int $changesBitmap,
    ) {}
}
