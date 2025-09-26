<?php

namespace App\Controller\Admin\quiz;

use App\Dto\Request\QuestionsSwapData;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AdminSwapQuestionsController extends AbstractController
{
    #[Route('/admin/quiz/questions/swap', name: 'admin_quiz_questions_swap', methods: ['POST'])]
    public function swap(
        #[MapRequestPayload]
        QuestionsSwapData $data,
        QuizServiceInterface $quizService,
    ): Response {
        try {
            $quizVersion = $quizService->load($data->quizId, version: $data->version);

            $result = $quizService->swapQuestion(
                quizVersionId: $quizVersion->getId(),
                firstQuestionId: $data->question1,
                secondQuestionId: $data->question2,
                isDraft: $quizVersion->getStatus()->isDraft(),
            );

            $quizService->resolveVersion($result->quizVersion, $result->changesBitmap);

            return $this->redirectToRoute('admin_quiz_view', ['id' => $data->quizId, 'version' => $result->quizVersion->getVersion()]);
        } catch (\Throwable $exception) {
            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], new Response()->setPrivate()->setCache(['no_cache' => true]));
        }
    }
}
