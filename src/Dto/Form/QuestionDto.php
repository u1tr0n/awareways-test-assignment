<?php

namespace App\Dto\Form;

use App\Entity\Category;
use App\Entity\Question;
use App\Entity\Tag;
use App\Enum\QuestionType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\UuidV7;

final class QuestionDto
{
    /**
     * @param UuidV7 $id
     * @param string $title
     * @param string $description
     * @param QuestionType $type
     * @param Collection<int, Tag> $tags
     * @param Category|null $category
     */
    public function __construct(
        public UuidV7 $id,
        public string $title,
        public string $description,
        public QuestionType $type,
        public Collection $tags,
        public ?Category $category = null,
    ) {}

    public static function fromEntity(Question $question): self
    {
        return new self(
            id: $question->getId(),
            title: $question->getTitle(),
            description: $question->getDescription(),
            type: $question->getType(),
            tags: $question->getTags(),
            category: $question->getCategory(),
        );
    }

    public static function createEmpty(): self
    {
        return new self(
            id: new UuidV7(),
            title: '',
            description: '',
            type: QuestionType::SINGLE_ANSWER,
            tags: new ArrayCollection(),
            category: null,
        );
    }

    public function fillEntity(Question $question): Question
    {
        $question
            ->setTitle($this->title)
            ->setDescription($this->description)
            ->setType($this->type)
            ->setCategory($this->category)
            ->clearTags()
        ;
        foreach ($this->tags as $tag) {
            $question->addTag($tag);
        }

        return $question;
    }
}
