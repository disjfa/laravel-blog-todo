<?php

namespace App\Filament\Pages;

use App\Enums\TodoStatus;
use App\Filament\Concerns\ResolvesCustomerTenant;
use App\Filament\Resources\Todos\Schemas\TodoForm;
use App\Models\Todo;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class TodoKanban extends Page
{
    use ResolvesCustomerTenant;

    protected string $view = 'filament.pages.todo-kanban';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?string $navigationLabel = 'Kanban';

    protected static ?string $title = 'Todo Kanban';

    protected static ?int $navigationSort = 2;

    protected string|Width|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTodo')
                ->label('New Todo')
                ->icon(Heroicon::OutlinedPlus)
                ->form(fn (Schema $schema): Schema => TodoForm::configure($schema)->model(Todo::class))
                ->action(function (array $data): void {
                    $tenant = $this->getCustomerTenant();

                    Todo::create([
                        ...$data,
                        'customer_id' => $tenant->id,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }),
        ];
    }

    public function editTodo(): Action
    {
        return Action::make('editTodo')
            ->form(fn (Schema $schema): Schema => TodoForm::configure($schema)->model(Todo::class))
            ->fillForm(function (array $arguments): array {
                $tenant = $this->getCustomerTenant();
                $recordId = $arguments['recordId'] ?? null;

                return Todo::query()
                    ->whereKey($recordId)
                    ->where('customer_id', $tenant->id)
                    ->firstOrFail()
                    ->toArray();
            })
            ->action(function (array $arguments, array $data): void {
                $tenant = $this->getCustomerTenant();
                $recordId = $arguments['recordId'] ?? null;

                Todo::query()
                    ->whereKey($recordId)
                    ->where('customer_id', $tenant->id)
                    ->firstOrFail()
                    ->update([
                        ...$data,
                        'updated_by' => auth()->id(),
                    ]);
            })
            ->modalHeading('Edit Todo');
    }

    protected function statuses(): Collection
    {
        return collect(TodoStatus::kanbanColumns());
    }

    protected function records(): Collection
    {
        $tenant = $this->getCustomerTenant();

        return Todo::query()
            ->where('customer_id', $tenant->id)
            ->ordered()
            ->with(['blog', 'platform'])
            ->get();
    }

    protected function getViewData(): array
    {
        $records = $this->records();

        $statuses = $this->statuses()->map(function (array $status) use ($records) {
            $status['records'] = $records->where('status', $status['id'])->values();

            return $status;
        });

        return ['statuses' => $statuses];
    }

    #[On('status-changed')]
    public function statusChanged(string $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        $tenant = $this->getCustomerTenant();

        Todo::query()
            ->where('id', $recordId)
            ->where('customer_id', $tenant->id)
            ->firstOrFail()
            ->update(['status' => $status]);

        Todo::setNewOrder($toOrderedIds);
    }

    #[On('sort-changed')]
    public function sortChanged(string $recordId, string $status, array $orderedIds): void
    {
        Todo::setNewOrder($orderedIds);
    }
}
