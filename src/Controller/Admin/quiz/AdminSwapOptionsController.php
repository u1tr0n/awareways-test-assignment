<?php

namespace App\Controller\Admin\quiz;

use App\Dto\Request\OptionsSwapData;
use App\Service\QuestionServiceInterface;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AdminSwapOptionsController extends AbstractController
{
    #[Route('/admin/quiz/options/swap', name: 'admin_quiz_options_swap', methods: ['POST'])]
    public function swap(
        #[MapRequestPayload]
        OptionsSwapData $data,
        QuestionServiceInterface $questionService,
        QuizServiceInterface $quizService,
    ): Response {
        try {
            $quizVersion = $quizService->load($data->quizId, version: $data->version);

            $questionResult = $questionService->swapOptions(
                questionId: $data->questionId,
                firstOptionId: $data->option1,
                secondOptionId: $data->option2,
                isDraft: $quizVersion->getStatus()->isDraft(),
            );

            $quizResult = $quizService->updateQuestion(
                quizVersionId: $quizVersion->getId(),
                targetQuestionId: $data->questionId,
                question: $questionResult->question,
                isDraft: $quizVersion->getStatus()->isDraft(),
            );

            $quizService->resolveVersion($quizResult->quizVersion, $questionResult->changesBitmap);

            $this->addFlash('success', 'options sort order changed.');

            return $this->redirectToRoute('admin_options_list', [
                'quizId' => $data->quizId,
                'questionId' => $questionResult->question->getId(),
                'version' => $quizResult->quizVersion->getVersion(),
            ]);
        } catch (\Throwable $exception) {
            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], new Response()->setPrivate()->setCache(['no_cache' => true]));
        }
    }
}
