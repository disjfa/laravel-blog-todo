<?php

namespace App\Filament\Resources\CustomerAssets;

use App\Filament\Resources\CustomerAssets\Pages\CreateCustomerAsset;
use App\Filament\Resources\CustomerAssets\Pages\EditCustomerAsset;
use App\Filament\Resources\CustomerAssets\Pages\ListCustomerAssets;
use App\Filament\Resources\CustomerAssets\Schemas\CustomerAssetForm;
use App\Filament\Resources\CustomerAssets\Tables\CustomerAssetsTable;
use App\Models\CustomerAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerAssetResource extends Resource
{
    protected static ?string $model = CustomerAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static bool $isScopedToTenant = true;

    public static function getNavigationGroup(): ?string
    {
        return 'Customer';
    }

    public static function getNavigationLabel(): string
    {
        return 'Assets';
    }

    public static function getModelLabel(): string
    {
        return 'asset';
    }

    public static function getPluralModelLabel(): string
    {
        return 'assets';
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerAssetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerAssetsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerAssets::route('/'),
            'create' => CreateCustomerAsset::route('/create'),
            'edit' => EditCustomerAsset::route('/{record}/edit'),
        ];
    }
}
