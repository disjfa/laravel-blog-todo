<?php

namespace App\Filament\Resources\Todos\Schemas;

use App\Enums\TodoStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TodoForm
{
    public static function components(): array
    {
        return [
            Section::make('Content')
                ->schema([
                    TextInput::make('title')
                        ->required(),
                    MarkdownEditor::make('content_markdown')
                        ->default(null),
                    TextInput::make('external_url')
                        ->url()
                        ->label(__('todo.label.external_url')),
                ])
                ->columnSpan(2),

            Section::make('Settings')
                ->schema([
                    Select::make('status')
                        ->options(TodoStatus::options())
                        ->required()
                        ->default(TodoStatus::Todo->value),
                    Select::make('blog_id')
                        ->relationship('blog', 'title'),
                    Select::make('platform_id')
                        ->relationship('platform', 'name'),
                    TextInput::make('position')
                        ->required(),
                    DateTimePicker::make('due_at')
                        ->required()
                        ->native(false),
                ]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components(static::components());
    }
}
