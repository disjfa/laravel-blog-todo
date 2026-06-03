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

    public function color(): string
    {
        return match ($this) {
            self::Todo => 'gray',
            self::Planned => 'blue',
            self::InProgress => 'yellow',
            self::Blocked => 'red',
            self::Done => 'green',
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

    public static function kanbanColumns(): array
    {
        return array_map(
            fn (self $status): array => [
                'id' => $status->value,
                'title' => $status->label(),
                'color' => $status->color(),
            ],
            self::cases(),
        );
    }
}
