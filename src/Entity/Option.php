<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: 'options')]
#[ORM\HasLifecycleCallbacks]
class Option
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column]
    private bool $isCorrect;

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

    public function isCorrect(): bool
    {
        return $this->isCorrect;
    }

    public function setCorrect(bool $correct): static
    {
        $this->isCorrect = $correct;

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
