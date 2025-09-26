<?php

namespace App\Dto\Request;

use Symfony\Component\Uid\UuidV7;

final readonly class QuestionsSwapData
{
    public function __construct(
        public UuidV7 $question1,
        public UuidV7 $question2,
        public UuidV7 $quizId,
        public string $version,
    ) {}
}
