<?php

namespace App\Controller\Admin\quiz;

use App\Repository\QuizRepository;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

final class AdminQuizViewController extends AbstractController
{
    public function __construct(
        private readonly QuizServiceInterface $quizService,
    ) {}

    #[Route('/admin/quiz/view/{id}/{version}', name: 'admin_quiz_view', defaults: ['version' => null], methods: ['GET'])]
    public function index(
        UuidV7 $id,
        ?string $version = null,
    ): Response {
        $response = new Response()->setPrivate()->setCache(['no_cache' => true])->setStatusCode(Response::HTTP_OK);

        try {
            $quiz = $this->quizService->load($id, $version);

            return $this->render('admin/quiz/admin_quiz_view.html.twig', [
                'quiz' => $quiz,
                'id' => $id,
            ]);
        } catch (\Throwable $exception) {
            $response->setStatusCode($exception->getCode() < 100 ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }
}
