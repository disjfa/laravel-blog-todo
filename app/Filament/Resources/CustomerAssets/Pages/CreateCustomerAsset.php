<?php

namespace App\Filament\Resources\CustomerAssets\Pages;

use App\Filament\Resources\CustomerAssets\CustomerAssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerAsset extends CreateRecord
{
    protected static string $resource = CustomerAssetResource::class;
}
