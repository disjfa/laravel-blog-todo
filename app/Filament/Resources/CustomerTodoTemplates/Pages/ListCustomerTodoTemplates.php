<?php

namespace App\Filament\Resources\CustomerTodoTemplates\Pages;

use App\Filament\Resources\CustomerTodoTemplates\CustomerTodoTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerTodoTemplates extends ListRecords
{
    protected static string $resource = CustomerTodoTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
