<?php

namespace App\Dto;

use App\Entity\Question;

final readonly class QuestionServiceResult
{
    public function __construct(
        public Question $question,
        public int $changesBitmap,
    ) {}
}
