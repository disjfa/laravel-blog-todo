<?php

namespace App\Filament\Resources\Blogs\RelationManagers;

use App\Enums\TodoStatus;
use App\Filament\Resources\Todos\Schemas\TodoForm;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TodosRelationManager extends RelationManager
{
    protected static string $relationship = 'todos';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(TodoForm::components());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label(__('todo.label.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('todo.label.status'))
                    ->badge()
                    ->color(fn (string $state): string => TodoStatus::colorFor($state))
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label(__('todo.label.due_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([]);
    }
}
