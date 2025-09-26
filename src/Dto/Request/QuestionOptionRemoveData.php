<?php

namespace App\Dto\Request;

use Symfony\Component\Uid\UuidV7;

final readonly class QuestionOptionRemoveData
{
    public function __construct(
        public UuidV7 $quizId,
        public UuidV7 $questionId,
        public UuidV7 $optionId,
        public string $version,
    ) {}
}
