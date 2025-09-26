<?php

namespace App\Controller\Admin\quiz;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminClientsListController extends AbstractController
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
    ) {}

    #[Route('/admin/clients/list', name: 'admin_clients_list', methods: ['GET'])]
    public function index(): Response
    {
        $response = new Response()->setPrivate()->setCache(['no_cache' => true])->setStatusCode(Response::HTTP_OK);

        try {
            $clients = $this->clientRepository->findAll();

            return $this->render('admin/client/admin_clients_list.html.twig', [
                'clients' => $clients,
            ]);
        } catch (\Throwable $exception) {
            $response->setStatusCode(0 === $exception->getCode() ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }
}
