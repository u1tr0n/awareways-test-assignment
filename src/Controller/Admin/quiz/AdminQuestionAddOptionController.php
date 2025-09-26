<?php

namespace App\Controller\Admin\quiz;

use App\Entity\Option;
use App\Form\OptionForm;
use App\Service\QuestionServiceInterface;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

class AdminQuestionAddOptionController extends AbstractController
{
    #[Route('/admin/quiz/{quizId}/{version}/question/{questionId}/options/add', name: 'admin_options_add', methods: ['GET', 'POST'])]
    public function create(
        UuidV7 $quizId,
        UuidV7 $questionId,
        string $version,
        Request $request,
        QuestionServiceInterface $questionService,
        QuizServiceInterface $quizService,
    ): Response {
        try {
            /** @var FormInterface<OptionForm> $form */
            $form = $this->createForm(OptionForm::class, new Option());

            $quizVersion = $quizService->load($quizId, $version);

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {

                    /** @var Option $option */
                    $option = $form->getData();
                    $questionResult = $questionService->addOption($questionId, $option, $quizVersion->getStatus()->isDraft());

                    $quizResult = $quizService->updateQuestion($quizVersion->getId(), $questionId, $questionResult->question, $quizVersion->getStatus()->isDraft());
                    $quizService->resolveVersion($quizResult->quizVersion, $questionResult->changesBitmap);

                    $this->addFlash('success', 'Option created successfully.');

                    return $this->redirectToRoute('admin_options_list', [
                        'quizId' => $quizId,
                        'version' => $quizResult->quizVersion->getVersion(),
                        'questionId' => $questionResult->question->getId(),
                    ]);
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
