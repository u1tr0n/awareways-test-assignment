<?php

namespace App\Controller\Admin\quiz;

use App\Entity\Client;
use App\Form\ClientForm;
use App\Form\QuestionForm;
use App\Service\ClientServiceInterface;
use App\Service\QuizServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\UuidV7;

class AdminCreateClientController extends AbstractController
{
    #[Route('/admin/client/create/{quizVersionId}', name: 'admin_client_create', methods: ['GET', 'POST'])]
    public function changeStatus(
        UuidV7 $quizVersionId,
        Request $request,
        QuizServiceInterface $quizService,
        ClientServiceInterface $clientService,
    ): Response {
        try {
            $quizVersion = $quizService->get($quizVersionId);

            if (true === $quizVersion->getStatus()->isDraft()) {
                $this->addFlash('error', 'You can not create client Quiz from Draft version.');

                return $this->redirectToRoute('admin_quiz_view', ['id' => $quizVersion->getQuiz()->getId(), 'version' => $quizVersion->getVersion()]);
            }

            /** @var FormInterface<ClientForm> $form */
            $form = $this->createForm(ClientForm::class, new Client()->setQuiz($quizVersion));

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {

                    $clientService->create($form);

                    $this->addFlash('success', 'Client created successfully.');

                    return $this->redirectToRoute('admin_clients_list');
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
