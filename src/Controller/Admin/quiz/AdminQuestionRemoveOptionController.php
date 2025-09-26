<?php

namespace App\Controller\Admin\quiz;

use App\Dto\Request\QuestionOptionRemoveData;
use App\Service\QuestionServiceInterface;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AdminQuestionRemoveOptionController extends AbstractController
{
    #[Route('/admin/options/remove', name: 'admin_options_remove', methods: ['POST'])]
    public function index(
        #[MapRequestPayload]
        QuestionOptionRemoveData $data,
        QuestionServiceInterface $questionService,
        QuizServiceInterface $quizService,
    ): Response {
        $response = new Response()->setPrivate()->setCache(['no_cache' => true])->setStatusCode(Response::HTTP_OK);

        try {
            $quizVersion = $quizService->load($data->quizId, $data->version);
            $questionResult = $questionService->removeOption($data->questionId, $data->optionId, $quizVersion->getStatus()->isDraft());
            $quizResult = $quizService->updateQuestion($quizVersion->getId(), $data->questionId, $questionResult->question, $quizVersion->getStatus()->isDraft());

            $quizService->resolveVersion($quizResult->quizVersion, $questionResult->changesBitmap);

            $this->addFlash('success', 'Option removed successfully.');

            return $this->redirectToRoute('admin_options_list', [
                'quizId' => $data->quizId,
                'version' => $quizResult->quizVersion->getVersion(),
                'questionId' => $questionResult->question->getId(),
            ]);
        } catch (\Throwable $exception) {
            $response->setStatusCode(0 === $exception->getCode() ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }
}
