<?php

namespace App\Filament\Pages;

use App\Models\Blog;
use App\Models\Platform;
use App\Models\Todo;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class TodoKanban extends Page
{
    protected string $view = 'filament.pages.todo-kanban';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?string $navigationLabel = 'Kanban';

    protected static ?string $title = 'Todo Kanban';

    protected static ?int $navigationSort = 2;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTodo')
                ->label('New Todo')
                ->icon(Heroicon::OutlinedPlus)
                ->form($this->todoFormFields())
                ->action(function (array $data): void {
                    $tenant = Filament::getTenant();
                    Todo::create([
                        ...$data,
                        'customer_id' => $tenant->id,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }),
        ];
    }

    public function editTodo(string $recordId): Action
    {
        return Action::make('editTodo')
            ->form($this->todoFormFields())
            ->fillForm(fn (): array => Todo::findOrFail($recordId)->toArray())
            ->action(function (array $data) use ($recordId): void {
                Todo::findOrFail($recordId)->update([
                    ...$data,
                    'updated_by' => auth()->id(),
                ]);
            })
            ->modalHeading('Edit Todo');
    }

    private function todoFormFields(): array
    {
        $tenant = Filament::getTenant();

        return [
            TextInput::make('title')
                ->required()
                ->columnSpanFull(),
            Select::make('status')
                ->options([
                    'todo' => 'Todo',
                    'planned' => 'Planned',
                    'in_progress' => 'In Progress',
                    'blocked' => 'Blocked',
                    'done' => 'Done',
                ])
                ->default('todo')
                ->required(),
            Select::make('platform_id')
                ->label('Platform')
                ->options(Platform::pluck('name', 'id'))
                ->searchable(),
            Select::make('blog_id')
                ->label('Blog')
                ->options(Blog::where('customer_id', $tenant->id)->pluck('title', 'id'))
                ->searchable(),
            DateTimePicker::make('due_at')
                ->required(),
            Textarea::make('content_markdown')
                ->label('Content')
                ->columnSpanFull(),
        ];
    }

    protected function statuses(): Collection
    {
        return collect([
            ['id' => 'todo', 'title' => 'Todo', 'color' => 'gray'],
            ['id' => 'planned', 'title' => 'Planned', 'color' => 'blue'],
            ['id' => 'in_progress', 'title' => 'In Progress', 'color' => 'yellow'],
            ['id' => 'blocked', 'title' => 'Blocked', 'color' => 'red'],
            ['id' => 'done', 'title' => 'Done', 'color' => 'green'],
        ]);
    }

    protected function records(): Collection
    {
        $tenant = Filament::getTenant();

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
        $tenant = Filament::getTenant();

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
