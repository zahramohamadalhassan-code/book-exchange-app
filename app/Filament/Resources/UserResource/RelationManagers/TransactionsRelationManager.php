<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'ownedTransactions';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('admin.relation.transactions');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('book_id')
                    ->label(__('admin.relation.book'))
                    ->relationship('book', 'title')
                    ->required(),
                Forms\Components\Select::make('requester_id')
                    ->label(__('admin.relation.requester'))
                    ->relationship('requester', 'full_name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('admin.relation.status'))
                    ->options([
                        'pending' => __('admin.relation.statuses.pending'),
                        'accepted' => __('admin.relation.statuses.accepted'),
                        'rejected' => __('admin.relation.statuses.rejected'),
                        'completed' => __('admin.relation.statuses.completed'),
                        'cancelled' => __('admin.relation.statuses.cancelled'),
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label(__('admin.relation.book'))
                    ->limit(25)
                    ->searchable(),
                Tables\Columns\TextColumn::make('requester.full_name')
                    ->label(__('admin.relation.requester')),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('admin.relation.status'))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('meeting_date')
                    ->label(__('admin.relation.meeting_date'))
                    ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.relation.created_at'))
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
