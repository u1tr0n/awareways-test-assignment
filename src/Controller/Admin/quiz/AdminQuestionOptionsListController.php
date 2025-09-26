<?php

namespace App\Controller\Admin\quiz;

use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

class AdminQuestionOptionsListController extends AbstractController
{
    public function __construct(
        private readonly QuestionRepository $questionRepository,
    ) {}

    #[Route('/admin/quiz/{quizId}/{version}/question/{questionId}/options', name: 'admin_options_list', methods: ['GET'])]
    public function index(
        UuidV7 $quizId,
        UuidV7 $questionId,
        string $version,
    ): Response {
        $response = new Response()->setPrivate()->setCache(['no_cache' => true])->setStatusCode(Response::HTTP_OK);

        try {
            $question = $this->questionRepository->findOneBy(['id' => $questionId]);
            if (null === $question) {
                throw $this->createNotFoundException("Question with ID={$questionId} not found");
            }

            return $this->render('admin/option/admin_options_list.html.twig', [
                'question' => $question,
                'quiz' => $quizId,
                'version' => $version,
            ]);
        } catch (\Throwable $exception) {
            $response->setStatusCode(0 === $exception->getCode() ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }
}
