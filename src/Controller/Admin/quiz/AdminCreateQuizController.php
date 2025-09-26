<?php

namespace App\Controller\Admin\quiz;

use App\Entity\Quiz;
use App\Form\QuizForm;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormInterface;

class AdminCreateQuizController extends AbstractController
{
    #[Route('/admin/quiz/create', name: 'admin_quiz_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        QuizServiceInterface $quizService,
    ): Response {
        try {

            $quiz = new Quiz();

            /** @var FormInterface<QuizForm> $form */
            $form = $this->createForm(QuizForm::class, $quiz);

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {

                    /** @var Quiz $formQuiz */
                    $formQuiz = $form->getData();
                    $quizVersion = $quizService->createQuiz($formQuiz);
                    $this->addFlash('success', 'Quiz created successfully.');

                    return $this->redirectToRoute('admin_quiz_view', ['id' => $quizVersion->getQuiz()->getId(), 'version' => $quizVersion->getVersion()]);
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
