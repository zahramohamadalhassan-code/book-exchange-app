<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfolistSection;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationGroup(): string
    {
        return __('admin.monitoring');
    }

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('admin.transaction.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.transaction.model_label_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin.transaction.section_data'))
                    ->schema([
                        Select::make('book_id')
                            ->label(__('admin.transaction.book'))
                            ->relationship('book', 'title')
                            ->disabled(),
                        Select::make('requester_id')
                            ->label(__('admin.transaction.requester'))
                            ->relationship('requester', 'full_name')
                            ->disabled(),
                        Select::make('owner_id')
                            ->label(__('admin.transaction.owner'))
                            ->relationship('owner', 'full_name')
                            ->disabled(),
                        Select::make('status')
                            ->label(__('admin.transaction.status'))
                            ->options([
                                'pending' => __('admin.transaction.statuses.pending'),
                                'accepted' => __('admin.transaction.statuses.accepted'),
                                'rejected' => __('admin.transaction.statuses.rejected'),
                                'completed' => __('admin.transaction.statuses.completed'),
                                'cancelled' => __('admin.transaction.statuses.cancelled'),
                            ])
                            ->disabled(),
                        DatePicker::make('meeting_date')
                            ->label(__('admin.transaction.meeting_date'))
                            ->disabled(),
                        TextInput::make('meeting_time')
                            ->label(__('admin.transaction.meeting_time'))
                            ->disabled(),
                        TextInput::make('meeting_location')
                            ->label(__('admin.transaction.meeting_location'))
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make(__('admin.transaction.section_data'))
                    ->schema([
                        TextEntry::make('book.title')
                            ->label(__('admin.transaction.book')),
                        TextEntry::make('requester.full_name')
                            ->label(__('admin.transaction.requester')),
                        TextEntry::make('owner.full_name')
                            ->label(__('admin.transaction.owner')),
                        TextEntry::make('status')
                            ->label(__('admin.transaction.status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'accepted' => 'info',
                                'rejected' => 'danger',
                                'completed' => 'success',
                                'cancelled' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => __('admin.transaction.statuses.pending'),
                                'accepted' => __('admin.transaction.statuses.accepted'),
                                'rejected' => __('admin.transaction.statuses.rejected'),
                                'completed' => __('admin.transaction.statuses.completed'),
                                'cancelled' => __('admin.transaction.statuses.cancelled'),
                                default => $state,
                            }),
                        TextEntry::make('meeting_date')
                            ->label(__('admin.transaction.meeting_date'))
                            ->date('Y-m-d'),
                        TextEntry::make('meeting_time')
                            ->label(__('admin.transaction.meeting_time')),
                        TextEntry::make('meeting_location')
                            ->label(__('admin.transaction.meeting_location')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label(__('admin.transaction.book'))
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('requester.full_name')
                    ->label(__('admin.transaction.requester'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner.full_name')
                    ->label(__('admin.transaction.owner'))
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('admin.transaction.status'))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('meeting_date')
                    ->label(__('admin.transaction.meeting_date'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.transaction.created_at'))
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('admin.transaction.status'))
                    ->options([
                        'pending' => __('admin.transaction.statuses.pending'),
                        'accepted' => __('admin.transaction.statuses.accepted'),
                        'rejected' => __('admin.transaction.statuses.rejected'),
                        'completed' => __('admin.transaction.statuses.completed'),
                        'cancelled' => __('admin.transaction.statuses.cancelled'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
