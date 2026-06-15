<?php

namespace App\Enums;

enum TodoStatus: string
{
    case Todo = 'todo';
    case Planned = 'planned';
    case InProgress = 'in_progress';
    case Blocked = 'blocked';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'Todo',
            self::Planned => 'Planned',
            self::InProgress => 'In Progress',
            self::Blocked => 'Blocked',
            self::Done => 'Done',
        };
    }

    public function color(): StatusColor
    {
        return match ($this) {
            self::Todo => StatusColor::Gray,
            self::Planned => StatusColor::Info,
            self::InProgress => StatusColor::Warning,
            self::Blocked => StatusColor::Danger,
            self::Done => StatusColor::Success,
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

    public static function kanbanColumns(): array
    {
        return array_map(
            fn (self $status): array => [
                'id' => $status->value,
                'title' => $status->label(),
                'color' => $status->color()->value,
                'header_color_classes' => $status->color()->kanbanHeaderClasses(),
            ],
            self::cases(),
        );
    }
}
