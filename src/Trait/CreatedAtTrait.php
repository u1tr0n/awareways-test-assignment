<?php
declare(strict_types=1);

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    protected \DateTimeImmutable $createdAt;

    #[ORM\PrePersist]
    public function persistCreatedAt(): void
    {
        $this->setCreatedAt($this->generateDate());
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt ?? $this->generateDate();
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    private function generateDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
