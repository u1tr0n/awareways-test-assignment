<?php

namespace App\Entity;

use App\Repository\QuestionOptionRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionOptionRepository::class)]
#[ORM\Table(name: 'questions_options')]
#[ORM\HasLifecycleCallbacks]
class QuestionOption
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\ManyToOne(inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false)]
    private Question $question;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private Option $option;

    #[ORM\Column]
    private int $sortPosition = 0;

    public function __construct(
        ?UuidV7 $id = null
    ) {
        $this->id = $id ?? new UuidV7();
    }

    public function getId(): UuidV7
    {
        return $this->id;
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

    public function getOption(): Option
    {
        return $this->option;
    }

    public function setOption(Option $option): static
    {
        $this->option = $option;

        return $this;
    }

    public function getSortPosition(): int
    {
        return $this->sortPosition;
    }

    public function setSortPosition(int $sortPosition): static
    {
        $this->sortPosition = $sortPosition;

        return $this;
    }
}
