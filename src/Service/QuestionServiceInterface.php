<?php

namespace App\Service;

use App\Dto\QuestionServiceResult;
use App\Entity\Option;
use App\Entity\Question;
use Symfony\Component\Uid\UuidV7;

interface QuestionServiceInterface
{
    public function get(UuidV7 $id): Question;
    public function addOption(UuidV7 $questionId, Option $option, bool $isDraft = false): QuestionServiceResult;
    public function removeOption(UuidV7 $questionId, UuidV7 $optionId, bool $isDraft = false): QuestionServiceResult;
    public function updateOption(UuidV7 $questionId, UuidV7 $targetOptionId, Option $option, bool $isDraft = false): QuestionServiceResult;
    public function swapOptions(UuidV7 $questionId, UuidV7 $firstOptionId, UuidV7 $secondOptionId, bool $isDraft = false): QuestionServiceResult;
}
