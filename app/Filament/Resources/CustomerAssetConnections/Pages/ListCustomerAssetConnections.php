<?php

namespace App\Filament\Resources\CustomerAssetConnections\Pages;

use App\Filament\Resources\CustomerAssetConnections\CustomerAssetConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerAssetConnections extends ListRecords
{
    protected static string $resource = CustomerAssetConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
