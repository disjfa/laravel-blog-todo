<?php

namespace App\Filament\Resources\CustomerAssetConnections\Pages;

use App\Enums\AssetDriver;
use App\Filament\Resources\CustomerAssetConnections\CustomerAssetConnectionResource;
use App\Models\CustomerAssetConnection;
use App\Services\AssetDrivers\AssetDriverFactory;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Arrayable;
use Throwable;

class EditCustomerAssetConnection extends EditRecord
{
    protected static string $resource = CustomerAssetConnectionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var CustomerAssetConnection $record */
        $record = $this->getRecord();

        $data['config_encrypted'] = $record->getDecryptedConfig();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Test connection')
                ->icon(Heroicon::OutlinedLink)
                ->color('info')
                ->action(function (): void {
                    $data = $this->form->getState();

                    $driver = $data['driver'] ?? null;
                    $driver = $driver instanceof AssetDriver ? $driver : AssetDriver::tryFrom((string) $driver);

                    if (! $driver) {
                        Notification::make()
                            ->title('Select a driver first')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        AssetDriverFactory::testConnection($driver, $data['config_encrypted'] ?? []);

                        $rawState = $this->form->getRawState();

                        if ($rawState instanceof Arrayable) {
                            $rawState = $rawState->toArray();
                        }

                        $rawState['last_validated_at'] = now()->toDateTimeString();

                        $this->form->fill($rawState);

                        Notification::make()
                            ->title('Connection succeeded')
                            ->body('The provided credentials were validated successfully.')
                            ->success()
                            ->send();
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->title('Connection failed')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }
}
