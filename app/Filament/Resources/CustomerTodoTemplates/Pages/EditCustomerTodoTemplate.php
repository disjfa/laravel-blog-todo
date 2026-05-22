<?php

namespace App\Filament\Resources\CustomerTodoTemplates\Pages;

use App\Filament\Resources\CustomerTodoTemplates\CustomerTodoTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerTodoTemplate extends EditRecord
{
    protected static string $resource = CustomerTodoTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
