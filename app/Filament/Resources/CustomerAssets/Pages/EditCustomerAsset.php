<?php

namespace App\Filament\Resources\CustomerAssets\Pages;

use App\Filament\Resources\CustomerAssets\CustomerAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerAsset extends EditRecord
{
    protected static string $resource = CustomerAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
