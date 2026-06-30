<?php

namespace App\Filament\Resources\CustomerAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('connection_id')
                    ->relationship(
                        'connection',
                        'name',
                        fn ($query) => $query->whereBelongsTo(filament()->getTenant(), 'customer'),
                    )
                    ->required()
                    ->disabled(fn (string $operation): bool => $operation === 'edit'),

                FileUpload::make('upload')
                    ->label('File')
                    ->storeFiles(false)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),

                TextInput::make('filename')
                    ->disabled()
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                TextInput::make('mime_type')
                    ->disabled()
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                TextInput::make('size_bytes')
                    ->disabled()
                    ->numeric()
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                TextInput::make('disk_driver')
                    ->disabled()
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                TextInput::make('path')
                    ->disabled()
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                TextInput::make('provider_asset_id')
                    ->disabled()
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
            ]);
    }
}
