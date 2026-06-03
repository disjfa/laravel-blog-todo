<?php

namespace App\Filament\Resources\Todos\Schemas;

use App\Enums\TodoStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TodoForm
{
    public static function components(): array
    {
        return [
            Select::make('blog_id')
                ->relationship('blog', 'title'),
            Select::make('platform_id')
                ->relationship('platform', 'name'),
            TextInput::make('title')
                ->required(),
            MarkdownEditor::make('content_markdown')
                ->default(null)
                ->columnSpanFull(),
            Select::make('status')
                ->options(TodoStatus::options())
                ->required()
                ->default(TodoStatus::Todo->value),
            TextInput::make('position')
                ->required(),
            DateTimePicker::make('due_at')
                ->required(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(static::components());
    }
}
