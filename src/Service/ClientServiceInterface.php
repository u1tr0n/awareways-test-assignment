<?php

namespace App\Service;

use App\Dto\ClientQuestion;
use App\Entity\Client;
use App\Exception\ClientNotFoundException;
use App\Exception\QuestionNotFoundException;
use App\Form\ClientForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Uid\UuidV7;

interface ClientServiceInterface
{
    /**
     * @param FormInterface<ClientForm> $form
     */
    public function create(FormInterface $form): Client;

    public function getQuestion(UuidV7 $clientId): ?ClientQuestion;

    /**
     * @param array<array-key, UuidV7> $answers
     *
     * @throws QuestionNotFoundException
     * @throws ClientNotFoundException
     */
    public function answer(UuidV7 $clientId, UuidV7 $questionId, array $answers): void;
}
