<?php

namespace App\Enums;

enum BlogStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('blog.status.draft'),
            self::Published => __('blog.status.published'),
            self::Archived => __('blog.status.archived'),
        };
    }

    public function color(): StatusColor
    {
        return match ($this) {
            self::Draft => StatusColor::Gray,
            self::Published => StatusColor::Success,
            self::Archived => StatusColor::Warning,
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    public static function colorFor(string $status): string
    {
        return self::tryFrom($status)?->color()->value ?? StatusColor::Gray->value;
    }
}
