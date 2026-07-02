<?php

namespace App\Filament\Resources\CustomerAssetConnections;

use App\Filament\Resources\CustomerAssetConnections\Pages\CreateCustomerAssetConnection;
use App\Filament\Resources\CustomerAssetConnections\Pages\EditCustomerAssetConnection;
use App\Filament\Resources\CustomerAssetConnections\Pages\ListCustomerAssetConnections;
use App\Filament\Resources\CustomerAssetConnections\Schemas\CustomerAssetConnectionForm;
use App\Filament\Resources\CustomerAssetConnections\Tables\CustomerAssetConnectionsTable;
use App\Models\CustomerAssetConnection;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerAssetConnectionResource extends Resource
{
    protected static ?string $model = CustomerAssetConnection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static bool $isScopedToTenant = true;

    protected static ?string $tenantOwnershipRelationshipName = 'customer';

    protected static ?string $tenantRelationshipName = 'assetConnection';

    public static function getNavigationGroup(): ?string
    {
        return 'Customer';
    }

    public static function getNavigationLabel(): string
    {
        return 'Asset Connections';
    }

    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return null;
        }

        return (string) CustomerAssetConnection::query()
            ->whereBelongsTo($tenant)
            ->count();
    }

    public static function getModelLabel(): string
    {
        return 'asset connection';
    }

    public static function getPluralModelLabel(): string
    {
        return 'asset connections';
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerAssetConnectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerAssetConnectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerAssetConnections::route('/'),
            'create' => CreateCustomerAssetConnection::route('/create'),
            'edit' => EditCustomerAssetConnection::route('/{record}/edit'),
        ];
    }
}
