<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Todos\TodoResource;
use App\Models\Todo;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingTodosWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Upcoming & Overdue Todos';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Todo::query()
                    ->where('customer_id', Filament::getTenant()?->id)
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
                        'todo'        => 'gray',
                        'planned'     => 'info',
                        'in_progress' => 'warning',
                        'blocked'     => 'danger',
                        'done'        => 'success',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todo'        => 'Todo',
                        'planned'     => 'Planned',
                        'in_progress' => 'In Progress',
                        'blocked'     => 'Blocked',
                        'done'        => 'Done',
                        default       => $state,
                    }),

                TextColumn::make('platform.name')
                    ->label('Platform')
                    ->placeholder('—'),

                TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime('M j, Y')
                    ->color(fn (Todo $record): string => $record->due_at->isPast() ? 'danger' : 'warning')
                    ->description(fn (Todo $record): string => $record->due_at->isPast()
                        ? 'Overdue by ' . $record->due_at->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE])
                        : 'Due ' . $record->due_at->diffForHumans()
                    ),
            ])
            ->emptyStateHeading('No upcoming or overdue todos')
            ->emptyStateDescription('All caught up!')
            ->defaultPaginationPageOption(10);
    }
}
