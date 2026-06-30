<?php

namespace App\Filament\Resources\CustomerAssets\Pages;

use App\Filament\Resources\CustomerAssets\CustomerAssetResource;
use App\Models\Customer;
use App\Models\CustomerAssetConnection;
use App\Services\AssetDrivers\AssetDriverFactory;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateCustomerAsset extends CreateRecord
{
    protected static string $resource = CustomerAssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Customer) {
            throw ValidationException::withMessages([
                'connection_id' => 'A customer tenant must be selected.',
            ]);
        }

        $connection = CustomerAssetConnection::query()
            ->whereBelongsTo($tenant, 'customer')
            ->find($data['connection_id'] ?? null);

        if (! $connection) {
            throw ValidationException::withMessages([
                'connection_id' => 'The selected connection is invalid for this customer.',
            ]);
        }

        $file = $data['upload'] ?? null;

        if (! $file instanceof TemporaryUploadedFile) {
            throw ValidationException::withMessages([
                'upload' => 'Please upload a file.',
            ]);
        }

        $driver = AssetDriverFactory::makeFromConnection($connection);

        $originalFilename = $file->getClientOriginalName();
        $path = sprintf(
            'customer-assets/%s/%s-%s',
            $tenant->getKey(),
            Str::uuid()->toString(),
            $originalFilename,
        );

        $uploadResult = $driver->upload(
            $path,
            $file->get(),
            $connection->getDecryptedConfig(),
        );

        $diskDriver = $connection->driver;

        return [
            'customer_id' => $tenant->getKey(),
            'uploaded_by' => auth()->id(),
            'connection_id' => $connection->getKey(),
            'disk_driver' => $diskDriver,
            'path' => $path,
            'provider_asset_id' => $uploadResult['provider_asset_id'] ?? null,
            'filename' => $originalFilename,
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'meta' => [
                'extension' => $file->getClientOriginalExtension(),
                'original_name' => $originalFilename,
            ],
        ];
    }
}
