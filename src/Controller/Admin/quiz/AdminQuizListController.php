<?php

namespace App\Controller\Admin\quiz;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminQuizListController extends AbstractController
{
    public function __construct(
        private readonly QuizRepository $quizRepository,
    ) {}

    #[Route('/admin/quiz/list', name: 'admin_quiz_list', methods: ['GET'])]
    public function index(): Response
    {
        $response = new Response()->setPrivate()->setCache(['no_cache' => true])->setStatusCode(Response::HTTP_OK);

        try {
            $quizzes = $this->quizRepository->findAll();
            return $this->render('admin/quiz/admin_quiz_list.html.twig', [
                'quizzes' => $quizzes,
            ]);
        } catch (\Throwable $exception) {
            $response->setStatusCode(0 === $exception->getCode() ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }
}
