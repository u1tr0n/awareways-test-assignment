<?php

namespace App\Enum;

enum QuizStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';

    public function getTitle(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
        };
    }

    public function getBsColor(): string
    {
        return match ($this) {
            self::DRAFT => 'danger',
            self::PUBLISHED => 'success',
        };
    }

    public function isDraft(): bool
    {
        return self::DRAFT === $this;
    }
}
