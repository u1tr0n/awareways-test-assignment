<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(
    name: 'clients',
    uniqueConstraints: [
        new UniqueConstraint(name: 'uniq_client', columns: ['name', 'version']),
    ]
)]
#[UniqueConstraint(name: 'uniq_client', columns: ['name', 'version'])]
#[ORM\HasLifecycleCallbacks]
class Client
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255)]
    private string $name;

    #[ORM\Column(length: 15)]
    #[Assert\Length(min: 1, max: 15)]
    private string $version = 'v1.0.0';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private QuizVersion $quiz;

    /**
     * @var Collection<int, ClientAnswer>
     */
    #[ORM\OneToMany(targetEntity: ClientAnswer::class, mappedBy: 'client')]
    private Collection $answers;

    public function __construct(?UuidV7 $id = null)
    {
        $this->id = $id ?? new UuidV7();
        $this->answers = new ArrayCollection();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getQuiz(): QuizVersion
    {
        return $this->quiz;
    }

    public function setQuiz(QuizVersion $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return Collection<int, ClientAnswer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(ClientAnswer $answer): static
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setClient($this);
        }

        return $this;
    }

    public function removeAnswer(ClientAnswer $answer): static
    {
        $this->answers->removeElement($answer);

        return $this;
    }
}
