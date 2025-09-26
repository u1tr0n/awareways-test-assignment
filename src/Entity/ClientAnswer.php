<?php

namespace App\Entity;

use App\Repository\ClientAnswerRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientAnswerRepository::class)]
#[ORM\Table(name: 'clients_answers')]
#[ORM\HasLifecycleCallbacks]
class ClientAnswer
{

    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Question $question;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Option $answer;

    #[ORM\Column]
    private bool $isCorrect;

    public function __construct(?UuidV7 $id = null)
    {
        $this->id = $id ?? new UuidV7();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getQuestion(): Question
    {
        return $this->question;
    }

    public function setQuestion(Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): Option
    {
        return $this->answer;
    }

    public function setAnswer(Option $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }
}
