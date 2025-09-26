<?php

namespace App\Entity;

use App\Enum\QuestionType;
use App\Repository\QuestionRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\Table(name: 'questions')]
#[ORM\HasLifecycleCallbacks]
class Question
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $description;

    #[ORM\Column(length: 20, enumType: QuestionType::class)]
    private QuestionType $type;

    /** @var array<array-key, non-empty-string> */
    #[ORM\Column(type: Types::JSON)]
    private array $meta = [];

    /**
     * @var Collection<int, QuestionOption>
     */
    #[ORM\OneToMany(targetEntity: QuestionOption::class, mappedBy: 'question')]
    #[ORM\OrderBy(['sortPosition' => 'ASC'])]
    private Collection $options;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    private Collection $tags;

    #[ORM\ManyToOne]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'referrals')]
    private ?self $ref = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'ref')]
    private Collection $referrals;

    public function __construct(
        ?UuidV7 $id = null
    ) {
        $this->id = $id ?? new UuidV7();
        $this->options = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->referrals = new ArrayCollection();
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): QuestionType
    {
        return $this->type;
    }

    public function setType(QuestionType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array<array-key, non-empty-string>
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param array<array-key, non-empty-string> $meta
     *
     * @return $this
     */
    public function setMeta(array $meta): static
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return Collection<int, QuestionOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(QuestionOption $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setQuestion($this);
        }

        return $this;
    }

    public function removeOption(QuestionOption $option): static
    {
        $this->options->removeElement($option);

        return $this;
    }

    /**
     * @param Collection<int, QuestionOption> $options
     */
    public function setOptions(Collection $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array<int, Tag> $tags
     * @return $this
     */
    public function setTags(array $tags): static
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function clearTags(): static
    {
        $this->tags->clear();

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

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
            // set the owning side to null (unless already changed)
            if ($referral->getRef() === $this) {
                $referral->setRef(null);
            }
        }

        return $this;
    }
}
