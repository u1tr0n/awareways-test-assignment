<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizRepository::class)]

#[ORM\Table(
    name: 'quizzes',
    uniqueConstraints: [
        new UniqueConstraint(name: 'uniq_quiz_title', columns: ['title']),
    ]
)]
#[UniqueConstraint(name: 'uniq_quiz_title', columns: ['title'])]
#[UniqueEntity(fields: ['title'], errorPath: 'title')]

#[ORM\HasLifecycleCallbacks]
class Quiz
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\Column(length: 255)]
    private string $title;

    /**
     * @var Collection<int, QuizVersion>
     */
    #[ORM\OneToMany(targetEntity: QuizVersion::class, mappedBy: 'quiz')]
    #[ORM\OrderBy(['version' => 'DESC'])]
    private Collection $versions;

    public function __construct(
        ?UuidV7 $id = null
    ) {
        $this->id = $id ?? new UuidV7();
        $this->versions = new ArrayCollection();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, QuizVersion>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(QuizVersion $version): static
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
            $version->setQuiz($this);
        }

        return $this;
    }

    public function removeVersion(QuizVersion $version): static
    {
        $this->versions->removeElement($version);

        return $this;
    }
}
