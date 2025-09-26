<?php

namespace App\Controller;

use App\Dto\Request\ClientAnswerData;
use App\Service\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

final class ClientQuizController extends AbstractController
{
    public function __construct(
        private readonly ClientService $clientService,
    ) {}

    #[Route('/quiz/{id}', name: 'app_client_quiz', methods: ['GET'])]
    public function clientQuiz(
        UuidV7 $id,
    ): Response {
        $response = new Response()->setPrivate()->setCache(['no_cache' => true])->setStatusCode(Response::HTTP_OK);

        try {
            $quiz = $this->clientService->getQuestion($id);

            return $this->render('main/client_quiz.html.twig', [
                'quiz' => $quiz,
            ], $response);
        } catch (\Throwable $exception) {
            $response->setStatusCode(0 === $exception->getCode() ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }

    #[Route('/quiz/{id}', name: 'app_client_quiz_answer', methods: ['POST'])]
    public function answer(
        UuidV7 $id,
        #[MapRequestPayload]
        ClientAnswerData $data,
    ): Response {
        $response = new Response()->setPrivate()->setCache(['no_cache' => true])->setStatusCode(Response::HTTP_OK);

        try {
            $this->clientService->answer(
                clientId: $id,
                questionId: $data->question,
                answers: $data->answers,
            );

            return $this->redirectToRoute('app_client_quiz', ['id' => $id]);
        } catch (\Throwable $exception) {
            $response->setStatusCode(0 === $exception->getCode() ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }
}
