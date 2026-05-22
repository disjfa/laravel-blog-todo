<?php

namespace App\Filament\Resources\CustomerAssets\Pages;

use App\Filament\Resources\CustomerAssets\CustomerAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerAssets extends ListRecords
{
    protected static string $resource = CustomerAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
