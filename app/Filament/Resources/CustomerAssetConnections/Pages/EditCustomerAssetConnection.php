<?php

namespace App\Filament\Resources\CustomerAssetConnections\Pages;

use App\Filament\Resources\CustomerAssetConnections\CustomerAssetConnectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerAssetConnection extends EditRecord
{
    protected static string $resource = CustomerAssetConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
