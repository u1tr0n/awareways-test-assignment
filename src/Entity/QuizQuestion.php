<?php

namespace App\Entity;

use App\Repository\QuizQuestionRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizQuestionRepository::class)]
#[ORM\Table(name: 'quizzes_questions')]
#[ORM\HasLifecycleCallbacks]
class QuizQuestion
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\ManyToOne(targetEntity: Question::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Question $question;

    #[ORM\ManyToOne(inversedBy: 'quizQuestions')]
    #[ORM\JoinColumn(nullable: false)]
    private QuizVersion $quizVersion;

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

    public function getQuizVersion(): QuizVersion
    {
        return $this->quizVersion;
    }

    public function setQuizVersion(QuizVersion $quizVersion): static
    {
        $this->quizVersion = $quizVersion;

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
