<?php

namespace App\Filament\Resources\CustomerAssets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uploaded_by')
                    ->default(null),
                Select::make('connection_id')
                    ->relationship('connection', 'id')
                    ->required(),
                TextInput::make('disk_driver')
                    ->required(),
                TextInput::make('path')
                    ->default(null),
                Textarea::make('public_url')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('provider_asset_id')
                    ->default(null),
                TextInput::make('filename')
                    ->required(),
                TextInput::make('mime_type')
                    ->required(),
                TextInput::make('size_bytes')
                    ->required()
                    ->numeric(),
                Textarea::make('meta')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
