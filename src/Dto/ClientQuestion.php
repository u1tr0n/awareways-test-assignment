<?php

namespace App\Dto;

use App\Entity\Client;
use App\Entity\Option;
use App\Entity\Question;
use Symfony\Component\Uid\UuidV7;

final readonly class ClientQuestion
{
    /**
     * @param Client $client
     * @param Question $question
     * @param array<array-key, Option> $options
     * @param int $questionsCount
     * @param int $answersCount
     */
    public function __construct(
        public Client $client,
        public Question $question,
        public array $options,
        public int $questionsCount,
        public int $answersCount,
    ) {}
}
