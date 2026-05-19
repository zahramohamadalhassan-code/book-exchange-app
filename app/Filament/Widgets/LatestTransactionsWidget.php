<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
                    ->with(['book', 'requester', 'owner'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label(__('admin.widget.book_col'))
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('requester.full_name')
                    ->label(__('admin.widget.requester')),
                Tables\Columns\TextColumn::make('owner.full_name')
                    ->label(__('admin.widget.owner')),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('admin.widget.status'))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.widget.date'))
                    ->dateTime('Y-m-d H:i'),
            ])
            ->heading(__('admin.widget.latest_transactions'));
    }
}
