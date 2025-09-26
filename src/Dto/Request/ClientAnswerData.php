<?php

namespace App\Dto\Request;

use Symfony\Component\Uid\UuidV7;

final readonly class ClientAnswerData
{
    /**
     * @param UuidV7 $question
     * @param array<array-key, UuidV7> $answers
     */
    public function __construct(
        public UuidV7 $question,
        public array $answers = [],
    ) {}
}
