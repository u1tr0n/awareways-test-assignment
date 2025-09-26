<?php

namespace App\Service;

use App\Dto\ClientQuestion;
use App\Entity\Client;
use App\Entity\ClientAnswer;
use App\Exception\ClientNotFoundException;
use App\Exception\OptionNotFoundException;
use App\Exception\QuestionNotFoundException;
use App\Repository\ClientAnswerRepository;
use App\Repository\ClientRepository;
use App\Repository\OptionRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Uid\UuidV7;

final readonly class ClientService implements ClientServiceInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ClientRepository $clientRepository,
        private ClientAnswerRepository $clientAnswerRepository,
        private QuestionRepository $questionRepository,
        private OptionRepository $optionRepository,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function create(FormInterface $form): Client
    {
        /** @var Client $client */
        $client = $form->getData();

        $this->em->persist($client);

        $this->em->flush();

        return $client;
    }

    /**
     * @throws ClientNotFoundException
     * @throws QuestionNotFoundException
     */
    public function getQuestion(UuidV7 $clientId): ?ClientQuestion
    {
        $client = $this->clientRepository->find($clientId);

        if (null === $client) {
            throw new ClientNotFoundException("Clint with ID={$clientId} not found.");
        }

        $answersCount = $this->clientAnswerRepository->getAnswersCount($clientId);

        $questionCount = $this->questionRepository->getClientQuestionsCount($clientId);
        if ($answersCount >= $questionCount) {
            return null;
        }

        $question = $this->questionRepository->getClientQuestion($clientId, $answersCount);

        if (null === $question) {
            throw new QuestionNotFoundException("Clint with ID={$clientId} not found.");
        }

        $options = $this->optionRepository->getQuestionOptions($question->getId());

        return new ClientQuestion(
            client: $client,
            question: $question,
            options: $options,
            questionsCount: $questionCount,
            answersCount: $answersCount,
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws OptionNotFoundException
     */
    public function answer(UuidV7 $clientId, UuidV7 $questionId, array $answers): void
    {
        $question = $this->questionRepository->find($questionId);

        if (null === $question) {
            throw new QuestionNotFoundException("Clint with ID={$clientId} not found.");
        }

        $client = $this->clientRepository->find($clientId);

        if (null === $client) {
            throw new ClientNotFoundException("Clint with ID={$clientId} not found.");
        }

        foreach ($answers as $answer) {
            $option = $this->optionRepository->find($answer);

            if (null === $option) {
                throw new OptionNotFoundException("Clint with ID={$answer} not found.");
            }

            $clientAnswer = new ClientAnswer()->setAnswer($option)->setClient($client)->setQuestion($question)->setIsCorrect($option->isCorrect());

            $this->em->persist($clientAnswer);
        }

        $this->em->flush();
    }
}
