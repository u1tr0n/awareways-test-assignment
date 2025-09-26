<?php

namespace App\Controller\Admin\quiz;

use App\Dto\Form\QuestionDto;
use App\Entity\Question;
use App\Form\QuestionForm;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

class AdminAddQuestionController extends AbstractController
{
    #[Route('/admin/quiz/{quizId}/create', name: 'admin_quiz_add_question', methods: ['GET', 'POST'])]
    public function create(
        UuidV7 $quizId,
        Request $request,
        QuizServiceInterface $quizService,
    ): Response {
        try {
            /** @var FormInterface<QuestionForm> $form */
            $form = $this->createForm(QuestionForm::class, QuestionDto::createEmpty());

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {

                    /** @var Question $question */
                    $question = $form->getData();
                    $result = $quizService->addQuestion($quizId, $question);
                    $quizService->resolveVersion($result->quizVersion, $result->changesBitmap);
                    $this->addFlash('success', 'Question created successfully.');

                    return $this->redirectToRoute('admin_quiz_view', ['id' => $result->quizVersion->getQuiz()->getId(), 'version' => $result->quizVersion->getVersion()]);
                }
            }

            return $this->render('admin/form.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Throwable $exception) {
            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], new Response()->setPrivate()->setCache(['no_cache' => true]));
        }
    }
}
