<?php

namespace App\Controller\Admin\quiz;

use App\Dto\Form\QuestionDto;
use App\Form\QuestionForm;
use App\Repository\QuestionRepository;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

class AdminEditQuestionController extends AbstractController
{
    #[Route('/admin/quiz/{quizId}/edit/{id}', name: 'admin_quiz_edit_question', methods: ['GET', 'POST'])]
    public function create(
        UuidV7 $quizId,
        UuidV7 $id,
        Request $request,
        QuizServiceInterface $quizService,
        QuestionRepository $questionRepository,
    ): Response {
        try {
            $question = $questionRepository->findOneBy(['id' => $id]);

            if (null === $question) {
                throw $this->createNotFoundException("Question with ID={$id} not found");
            }

            /** @var FormInterface<QuestionForm> $form */
            $form = $this->createForm(QuestionForm::class, QuestionDto::fromEntity($question));

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    /** @var QuestionDto $questionDto */
                    $questionDto = $form->getData();

                    if ($form->has('leftColumnTitle') && $form->has('rightColumnTitle')) {
                        $leftColumnTitle = $form->get('leftColumnTitle')->getData();
                        $rightColumnTitle = $form->get('rightColumnTitle')->getData();

                        if (is_string($leftColumnTitle) && is_string($rightColumnTitle)) {
                            /** @var array<array-key, non-empty-string> $meta */
                            $meta = [$leftColumnTitle, $rightColumnTitle];
                            $questionDto->meta = $meta;
                        }
                    }

                    $result = $quizService->updateQuestion(
                        quizVersionId: $quizId,
                        targetQuestionId: $id,
                        question: $questionDto,
                    );
                    $quizService->resolveVersion($result->quizVersion, $result->changesBitmap);
                    $this->addFlash('success', 'Question updated successfully.');

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
