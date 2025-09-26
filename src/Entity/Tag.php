<?php

namespace App\Entity;

use App\Repository\TagRepository;
use App\Trait\CreatedAtTrait;
use App\Trait\UpdatedAtTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tags')]
#[UniqueConstraint(name: 'uniq_tag_title', columns: ['title'])]
#[UniqueEntity(fields: ['title'], errorPath: 'title')]
#[ORM\HasLifecycleCallbacks]
class Tag
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Assert\Uuid]
    private UuidV7 $id;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 1, max: 255)]
    private string $title;

    public function __construct(?UuidV7 $id = null)
    {
        $this->id = $id ?? new UuidV7();
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
}
