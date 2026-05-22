<?php

namespace App\Filament\Resources\CustomerTodoTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerTodoTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('platform_id')
                    ->relationship('platform', 'name')
                    ->required(),
                TextInput::make('title_template')
                    ->required(),
                Textarea::make('body_template')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('default_status')
                    ->required()
                    ->default('todo'),
                TextInput::make('due_offset_iso8601')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
