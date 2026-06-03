<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesCustomerTenant;
use App\Filament\Resources\Todos\TodoResource;
use App\Models\Todo;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingTodosWidget extends BaseWidget
{
    use ResolvesCustomerTenant;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Upcoming & Overdue Todos';

    public function table(Table $table): Table
    {
        $tenant = $this->getCustomerTenant();

        return $table
            ->query(
                fn (): Builder => Todo::query()
                    ->where('customer_id', $tenant->id)
                    ->whereNotIn('status', ['done'])
                    ->whereNotNull('due_at')
                    ->where('due_at', '<=', now()->addDays(7))
                    ->orderBy('due_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(60)
                    ->url(fn (Todo $record): string => TodoResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray',
                        'planned' => 'info',
                        'in_progress' => 'warning',
                        'blocked' => 'danger',
                        'done' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todo' => 'Todo',
                        'planned' => 'Planned',
                        'in_progress' => 'In Progress',
                        'blocked' => 'Blocked',
                        'done' => 'Done',
                        default => $state,
                    }),

                TextColumn::make('platform.name')
                    ->label('Platform')
                    ->placeholder('—'),

                TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime('M j, Y')
                    ->color(fn (Todo $record): string => Carbon::parse((string) $record->due_at)->isPast() ? 'danger' : 'warning')
                    ->description(fn (Todo $record): string => Carbon::parse((string) $record->due_at)->isPast()
                        ? 'Overdue by '.Carbon::parse((string) $record->due_at)->diffForHumans(['parts' => 1, 'syntax' => CarbonInterface::DIFF_ABSOLUTE])
                        : 'Due '.Carbon::parse((string) $record->due_at)->diffForHumans()
                    ),
            ])
            ->emptyStateHeading('No upcoming or overdue todos')
            ->emptyStateDescription('All caught up!')
            ->defaultPaginationPageOption(10);
    }
}
