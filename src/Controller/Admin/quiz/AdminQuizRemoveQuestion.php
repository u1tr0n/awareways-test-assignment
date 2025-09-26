<?php

namespace App\Controller\Admin\quiz;

use App\Dto\Request\QuizQuestionRemoveData;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AdminQuizRemoveQuestion extends AbstractController
{
    #[Route('/admin/quiz/question/remove', name: 'admin_quiz_question_remove', methods: ['POST'])]
    public function changeStatus(
        #[MapRequestPayload]
        QuizQuestionRemoveData $data,
        QuizServiceInterface $quizService,
    ): Response {
        try {
            $result = $quizService->removeQuestion(
                quizVersionId: $data->quizId,
                questionId: $data->questionId,
            );

            $quizService->resolveVersion($result->quizVersion, $result->changesBitmap);

            $this->addFlash('success', 'Quiz question was removed.');

            return $this->redirectToRoute('admin_quiz_view', ['id' => $result->quizVersion->getQuiz()->getId(), 'version' => $result->quizVersion->getVersion()]);
        } catch (\Throwable $exception) {
            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], new Response()->setPrivate()->setCache(['no_cache' => true]));
        }
    }
}
