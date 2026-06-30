<?php

namespace App\Filament\Pages;

use App\Enums\TodoStatus;
use App\Filament\Concerns\ResolvesCustomerTenant;
use App\Filament\Resources\Todos\Schemas\TodoForm;
use App\Models\Todo;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class TodoCalendar extends Page
{
    use ResolvesCustomerTenant;

    protected string $view = 'filament.pages.todo-calendar';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Calendar';

    protected static ?string $title = 'Todo Calendar';

    protected static ?int $navigationSort = 3;

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

                $this->dispatch('todos-updated');
            })
            ->modalHeading('Edit Todo');
    }

    protected function getViewData(): array
    {
        return [
            'events' => $this->calendarEvents(),
        ];
    }

    public function refreshEvents(): array
    {
        return $this->calendarEvents()->all();
    }

    protected function calendarEvents(): Collection
    {
        $tenant = $this->getCustomerTenant();

        return Todo::query()
            ->where('customer_id', $tenant->id)
            ->whereNotNull('due_at')
            ->where('status', '!=', 'done')
            ->with('platform')
            ->get()
            ->map(function ($todo): array {
                /** @var Todo $todo */
                $dueAt = Carbon::parse($todo->due_at);

                $platformName = data_get($todo, 'platform.name');

                return [
                    'id' => $todo->id,
                    'title' => (filled($platformName) ? '['.$platformName.'] ' : '').$todo->title,
                    'start' => $dueAt->toAtomString(),
                    'color' => $this->hexForStatus($todo->status),
                ];
            })
            ->filter(fn (array $event): bool => filled($event['start']))
            ->values();
    }

    private function hexForStatus(string $status): string
    {
        return match (TodoStatus::tryFrom($status)?->color()->value) {
            'info' => '#3b82f6',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            'success' => '#22c55e',
            default => '#6b7280',
        };
    }
}
