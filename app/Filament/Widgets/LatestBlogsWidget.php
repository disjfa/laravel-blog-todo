<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Blogs\BlogResource;
use App\Models\Blog;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestBlogsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Latest Blogs';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Blog::query()
                    ->where('customer_id', Filament::getTenant()?->id)
                    ->latest()
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(60)
                    ->url(fn (Blog $record): string => BlogResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'info',
                        'archived' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('publish_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
