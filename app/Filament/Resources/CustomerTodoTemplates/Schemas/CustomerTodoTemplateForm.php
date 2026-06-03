<?php

namespace App\Filament\Resources\CustomerTodoTemplates\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
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
                MarkdownEditor::make('body_template')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('default_status')
                    ->required()
                    ->default('todo'),
                Select::make('due_offset_iso8601')
                    ->options([
                        'PT1H' => 'In 1 hour',
                        'PT4H' => 'In 4 hours',
                        'P1D' => 'In 1 day',
                        'P2D' => 'In 2 days',
                        'P3D' => 'In 3 days',
                        'P7D' => 'In 1 week',
                        'P14D' => 'In 2 weeks',
                        'P1M' => 'In 1 month',
                    ])
                    ->searchable()
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
