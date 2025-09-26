<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminMainController extends AbstractController
{
    #[Route('/admin/', name: 'admin_main', methods: ['GET'])]
    public function index(): Response
    {
        $response = new Response()->setPrivate()->setStatusCode(Response::HTTP_OK);

        try {
            return $this->render('admin/index.html.twig');
        } catch (\Throwable $exception) {
            $response->setStatusCode(0 === $exception->getCode() ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode());

            return $this->render('main/error.html.twig', [
                'exception' => $exception,
            ], $response);
        }
    }
}
