<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Content')
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('excerpt')
                            ->default(null)
                            ->rows(4),
                        MarkdownEditor::make('content_markdown')
                            ->required(),
                    ])
                    ->columnSpan(2),

                Section::make('Settings')
                    ->schema([
                        TextInput::make('status')
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('publish_at'),
                    ]),
            ]);
    }
}
