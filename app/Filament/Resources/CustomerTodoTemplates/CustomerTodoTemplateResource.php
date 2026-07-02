<?php

namespace App\Filament\Resources\CustomerTodoTemplates;

use App\Filament\Resources\CustomerTodoTemplates\Pages\CreateCustomerTodoTemplate;
use App\Filament\Resources\CustomerTodoTemplates\Pages\EditCustomerTodoTemplate;
use App\Filament\Resources\CustomerTodoTemplates\Pages\ListCustomerTodoTemplates;
use App\Filament\Resources\CustomerTodoTemplates\Schemas\CustomerTodoTemplateForm;
use App\Filament\Resources\CustomerTodoTemplates\Tables\CustomerTodoTemplatesTable;
use App\Models\CustomerTodoTemplate;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerTodoTemplateResource extends Resource
{
    protected static ?string $model = CustomerTodoTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationGroup(): ?string
    {
        return 'Customer';
    }

    public static function getNavigationLabel(): string
    {
        return 'Todo Templates';
    }

    public static function getModelLabel(): string
    {
        return 'todo template';
    }

    public static function getPluralModelLabel(): string
    {
        return 'todo templates';
    }

    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return null;
        }

        return (string) CustomerTodoTemplate::query()
            ->whereBelongsTo($tenant)
            ->where('is_active', true)
            ->count();
    }

    public static function form(Schema $schema): Schema
    {
        return CustomerTodoTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerTodoTemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerTodoTemplates::route('/'),
            'create' => CreateCustomerTodoTemplate::route('/create'),
            'edit' => EditCustomerTodoTemplate::route('/{record}/edit'),
        ];
    }
}
