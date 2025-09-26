<?php

namespace App\Dto\Request;

use Symfony\Component\Uid\UuidV7;

final readonly class OptionsSwapData
{
    public function __construct(
        public UuidV7 $option1,
        public UuidV7 $option2,
        public UuidV7 $questionId,
        public UuidV7 $quizId,
        public string $version,
    ) {}
}
