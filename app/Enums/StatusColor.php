<?php

namespace App\Enums;

enum StatusColor: string
{
    case Gray = 'gray';
    case Info = 'info';
    case Warning = 'warning';
    case Danger = 'danger';
    case Success = 'success';

    public function kanbanHeaderClasses(): string
    {
        return match ($this) {
            self::Gray => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300',
            self::Info => 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
            self::Warning => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300',
            self::Danger => 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300',
            self::Success => 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
        };
    }
}
