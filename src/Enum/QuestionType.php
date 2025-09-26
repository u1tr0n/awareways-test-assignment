<?php

namespace App\Enum;

enum QuestionType: string
{
    case SINGLE_ANSWER = 'single_answer';
    case MULTIPLE_ANSWER = 'multiple_answer';
    case DRAG_AND_DROP_ANSWER = 'drag_and_drop_answer';

    public function getTitle(): string
    {
        return match ($this) {
            self::MULTIPLE_ANSWER => 'Multiple Answer',
            self::SINGLE_ANSWER => 'Single Answer',
            self::DRAG_AND_DROP_ANSWER => 'Drag And Drop Answer',
        };
    }

    public function getBsColor(): string
    {
        return match ($this) {
            self::MULTIPLE_ANSWER => 'primary',
            self::SINGLE_ANSWER => 'success',
            self::DRAG_AND_DROP_ANSWER => 'warning',
        };
    }
}
