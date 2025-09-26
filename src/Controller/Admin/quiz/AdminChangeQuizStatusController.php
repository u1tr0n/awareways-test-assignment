<?php

namespace App\Controller\Admin\quiz;

use App\Dto\Request\QuizStatusChangeData;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AdminChangeQuizStatusController extends AbstractController
{
    #[Route('/admin/quiz/change_status', name: 'admin_quiz_change_status', methods: ['POST'])]
    public function changeStatus(
        #[MapRequestPayload]
        QuizStatusChangeData $data,
        QuizServiceInterface $quizService,
    ): Response {
        try {
            $quizVersion = $quizService->changeQuizStatus($data->id, $data->isDraft);

            $this->addFlash('success', 'Quiz status has been changed.');

            return $this->redirectToRoute('admin_quiz_view', ['id' => $quizVersion->getQuiz()->getId(), 'version' => $quizVersion->getVersion()]);
        } catch (\Throwable $exception) {
            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], new Response()->setPrivate()->setCache(['no_cache' => true]));
        }
    }
}
