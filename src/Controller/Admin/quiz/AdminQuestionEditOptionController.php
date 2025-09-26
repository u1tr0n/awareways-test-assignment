<?php

namespace App\Controller\Admin\quiz;

use App\Entity\Option;
use App\Form\OptionForm;
use App\Repository\OptionRepository;
use App\Service\Cloner\OptionCloner;
use App\Service\QuestionServiceInterface;
use App\Service\QuizServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

class AdminQuestionEditOptionController extends AbstractController
{
    #[Route('/admin/quiz/{quizId}/{version}/question/{questionId}/options/edit/{optionId}', name: 'admin_options_edit', methods: ['GET', 'POST'])]
    public function create(
        UuidV7 $quizId,
        UuidV7 $questionId,
        UuidV7 $optionId,
        string $version,
        Request $request,
        QuestionServiceInterface $questionService,
        QuizServiceInterface $quizService,
        OptionRepository $optionRepository,
        OptionCloner $optionCloner,
        EntityManagerInterface $em,
    ): Response {
        try {
            $option = $optionRepository->findOneBy(['id' => $optionId]);

            if (null === $option) {
                throw $this->createNotFoundException("Option with ID=\"{$optionId}\" not found");
            }

            $option = $optionCloner->clone($option);

            /** @var FormInterface<OptionForm> $form */
            $form = $this->createForm(OptionForm::class, $option);

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $quizVersion = $quizService->load($quizId, $version);

                    /** @var Option $formOption */
                    $formOption = $form->getData();
                    $questionResult = $questionService->updateOption(
                        questionId: $questionId,
                        targetOptionId: $optionId,
                        option: $optionCloner->clone($formOption),
                        isDraft: $quizVersion->getStatus()->isDraft(),
                    );

                    $quizResult = $quizService->updateQuestion(
                        quizVersionId: $quizVersion->getId(),
                        targetQuestionId: $questionId,
                        question: $questionResult->question,
                        isDraft: $quizVersion->getStatus()->isDraft(),
                    );

                    $quizService->resolveVersion($quizResult->quizVersion, $questionResult->changesBitmap);

                    $this->addFlash('success', 'Option updated successfully.');

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
