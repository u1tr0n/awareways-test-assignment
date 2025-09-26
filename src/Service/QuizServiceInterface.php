<?php

namespace App\Service;

use App\Dto\Form\QuestionDto;
use App\Dto\QuizVersionServiceResult;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\QuizVersion;
use App\Exception\QuizNotFoundException;
use Symfony\Component\Uid\UuidV7;

interface QuizServiceInterface
{
    public function load(UuidV7 $id, ?string $version = null): QuizVersion;

    public function get(UuidV7 $id): QuizVersion;

    public function createQuiz(Quiz $quiz): QuizVersion;

    public function addQuestion(UuidV7 $quizVersionId, Question|QuestionDto $question, ?bool $isDraft = null): QuizVersionServiceResult;

    public function removeQuestion(UuidV7 $quizVersionId, UuidV7 $questionId, ?bool $isDraft = null): QuizVersionServiceResult;

    public function updateQuestion(UuidV7 $quizVersionId, UuidV7 $targetQuestionId, Question|QuestionDto $question, ?bool $isDraft = null): QuizVersionServiceResult;

    public function swapQuestion(UuidV7 $quizVersionId, UuidV7 $firstQuestionId, UuidV7 $secondQuestionId, ?bool $isDraft = null): QuizVersionServiceResult;

    /**
     * @throws QuizNotFoundException
     */
    public function changeQuizStatus(UuidV7 $quizVersionId, ?bool $isDraft = null): QuizVersion;

    public function resolveVersion(QuizVersion $quizVersion, int $newBitmask): void;
}
