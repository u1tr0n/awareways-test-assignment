<?php

namespace App\Entity;

use App\Enum\QuizStatus;
use App\Repository\QuizVersionRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizVersionRepository::class)]
#[ORM\Table(name: 'quizzes_versions')]
#[UniqueConstraint(name: 'uniq_quiz_version_per_quiz', columns: ['quiz_id', 'version'])]
#[UniqueEntity(fields: ['title'], errorPath: 'title')]
#[Index(name: 'idx_quiz_versions_quiz_version_status', fields: ['quiz_id', 'version', 'status'])]
#[Index(name: 'idx_quiz_versions_quiz_status', fields: ['quiz_id', 'status'])]
#[ORM\HasLifecycleCallbacks]
class QuizVersion
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\Column(length: 20)]
    #[Assert\Length(min: 5, max: 20)]
    private string $version = '0.0.0';

    /**
     * @var Collection<int, QuizQuestion>
     */
    #[ORM\OneToMany(targetEntity: QuizQuestion::class, mappedBy: 'quizVersion')]
    #[ORM\OrderBy(['sortPosition' => 'ASC'])]
    private Collection $quizQuestions;

    #[ORM\ManyToOne(inversedBy: 'versions')]
    #[ORM\JoinColumn(nullable: false)]
    private Quiz $quiz;

    #[ORM\Column(length: 20, nullable: false, enumType: QuizStatus::class, options: ['default' => QuizStatus::DRAFT->value])]
    private QuizStatus $status = QuizStatus::DRAFT;

    #[ORM\Column(options: ['default' => 0b000])]
    private int $currentBitmask = 0b000;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'referrals')]
    private ?self $ref = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'ref')]
    private Collection $referrals;

    public function __construct(?UuidV7 $id = null)
    {
        $this->id = $id ?? new UuidV7();
        $this->quizQuestions = new ArrayCollection();
        $this->referrals = new ArrayCollection();
    }

    public function getId(): UuidV7
    {
        return $this->id;
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

    /**
     * @return Collection<int, QuizQuestion>
     */
    public function getQuizQuestions(): Collection
    {
        return $this->quizQuestions;
    }

    public function addQuizQuestion(QuizQuestion $quizQuestion): static
    {
        if (!$this->quizQuestions->contains($quizQuestion)) {
            $this->quizQuestions->add($quizQuestion);
            $quizQuestion->setQuizVersion($this);
        }

        return $this;
    }

    public function removeQuizQuestion(QuizQuestion $quizQuestion): static
    {
        $this->quizQuestions->removeElement($quizQuestion);

        return $this;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    public function getStatus(): QuizStatus
    {
        return $this->status;
    }

    public function setStatus(QuizStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCurrentBitmask(): int
    {
        return $this->currentBitmask;
    }

    public function setCurrentBitmask(int $currentBitmask): static
    {
        $this->currentBitmask = $currentBitmask;

        return $this;
    }

    public function getRef(): ?self
    {
        return $this->ref;
    }

    public function setRef(?self $ref): static
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReferrals(): Collection
    {
        return $this->referrals;
    }

    public function addReferral(self $referral): static
    {
        if (!$this->referrals->contains($referral)) {
            $this->referrals->add($referral);
            $referral->setRef($this);
        }

        return $this;
    }

    public function removeReferral(self $referral): static
    {
        if ($this->referrals->removeElement($referral)) {
            if ($referral->getRef() === $this) {
                $referral->setRef(null);
            }
        }

        return $this;
    }
}
