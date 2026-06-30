<?php

namespace App\Filament\Resources\CustomerAssetConnections\Schemas;

use App\Enums\AssetDriver;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerAssetConnectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),

                Select::make('driver')
                    ->options(collect(AssetDriver::cases())
                        ->mapWithKeys(fn (AssetDriver $driver) => [$driver->value => $driver->label()])
                        ->toArray())
                    ->required()
                    ->live()
                    ->columnSpanFull(),

                Section::make('Credentials')
                    ->description('Enter the credentials for your '.'selected driver')
                    ->columnSpanFull()
                    ->schema([
                        // S3 Credentials
                        TextInput::make('config_encrypted.access_key')
                            ->label('AWS Access Key')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::S3->value),
                        TextInput::make('config_encrypted.secret_key')
                            ->label('AWS Secret Key')
                            ->password()
                            ->revealable()
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::S3->value),
                        TextInput::make('config_encrypted.region')
                            ->label('Region')
                            ->required()
                            ->default('us-east-1')
                            ->visible(fn ($get) => $get('driver') === AssetDriver::S3->value),
                        TextInput::make('config_encrypted.bucket')
                            ->label('Bucket Name')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::S3->value),
                        TextInput::make('config_encrypted.endpoint')
                            ->label('Endpoint (Optional)')
                            ->helperText('For S3-compatible services like DigitalOcean Spaces')
                            ->visible(fn ($get) => $get('driver') === AssetDriver::S3->value),

                        // FTP Credentials
                        TextInput::make('config_encrypted.host')
                            ->label('FTP Host')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::FTP->value),
                        TextInput::make('config_encrypted.username')
                            ->label('Username')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::FTP->value),
                        TextInput::make('config_encrypted.password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::FTP->value),
                        TextInput::make('config_encrypted.root')
                            ->label('Root Path')
                            ->default('/public_html')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::FTP->value),
                        TextInput::make('config_encrypted.url')
                            ->label('Base URL')
                            ->placeholder('https://example.com')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::FTP->value),

                        // Cloudinary Credentials
                        TextInput::make('config_encrypted.cloud_name')
                            ->label('Cloud Name')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::CLOUDINARY->value),
                        TextInput::make('config_encrypted.api_key')
                            ->label('API Key')
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::CLOUDINARY->value),
                        TextInput::make('config_encrypted.api_secret')
                            ->label('API Secret')
                            ->password()
                            ->revealable()
                            ->required()
                            ->visible(fn ($get) => $get('driver') === AssetDriver::CLOUDINARY->value),
                    ]),

                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('last_validated_at')
                    ->native(false),
            ]);
    }
}
