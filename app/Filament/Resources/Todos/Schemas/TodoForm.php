<?php

namespace App\Filament\Resources\Todos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TodoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('blog_id')
                    ->relationship('blog', 'title'),
                Select::make('platform_id')
                    ->relationship('platform', 'name'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('content_markdown')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('todo'),
                TextInput::make('position')
                    ->required(),
                DateTimePicker::make('due_at')
                    ->required(),
                TextInput::make('created_by')
                    ->default(null),
                TextInput::make('updated_by')
                    ->default(null),
            ]);
    }
}
