<?php

namespace App\Filament\Resources\CustomerAssets\Tables;

use App\Models\CustomerAsset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Js;

class CustomerAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview_url')
                    ->label('Image')
                    ->state(fn (CustomerAsset $record): ?string => $record->public_url)
                    ->imageHeight(56)
                    ->extraImgAttributes(fn (?string $state): array => [
                        'class' => filled($state) ? 'cursor-pointer' : null,
                        'x-on:click.prevent.stop' => filled($state)
                            ? 'window.navigator.clipboard.writeText('.Js::from($state)."); \$tooltip('Public URL copied', { theme: \$store.theme, timeout: 2000 })"
                            : null,
                    ]),
                TextColumn::make('connection.name')
                    ->label('Connection')
                    ->searchable(),
                TextColumn::make('filename')
                    ->searchable(),
                TextColumn::make('size_bytes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
