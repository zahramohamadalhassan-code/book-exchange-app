<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers\TransactionsRelationManager;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function getNavigationGroup(): string
    {
        return __('admin.content_management');
    }

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('admin.book.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.book.model_label_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.book.section_data'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('admin.book.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author')
                            ->label(__('admin.book.author'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('user_id')
                            ->label(__('admin.book.user'))
                            ->relationship('user', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('category_id')
                            ->label(__('admin.book.category'))
                            ->relationship('category', 'department_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('condition')
                            ->label(__('admin.book.condition'))
                            ->options([
                                'excellent' => __('admin.book.conditions.excellent'),
                                'good' => __('admin.book.conditions.good'),
                                'fair' => __('admin.book.conditions.fair'),
                                'poor' => __('admin.book.conditions.poor'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('offer_type')
                            ->label(__('admin.book.offer_type'))
                            ->options([
                                'sale' => __('admin.book.offer_types.sale'),
                                'exchange' => __('admin.book.offer_types.exchange'),
                                'donate' => __('admin.book.offer_types.donate'),
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label(__('admin.book.price'))
                            ->numeric()
                            ->prefix(__('admin.book.currency_syp'))
                            ->visible(fn (callable $get) => $get('offer_type') === 'sale'),
                        Forms\Components\Select::make('status')
                            ->label(__('admin.book.status'))
                            ->options([
                                'available' => __('admin.book.statuses.available'),
                                'pending' => __('admin.book.statuses.pending'),
                                'sold' => __('admin.book.statuses.sold'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('moderation_status')
                            ->label(__('admin.book.moderation_status'))
                            ->options([
                                'pending' => __('admin.book.moderation_statuses.pending'),
                                'approved' => __('admin.book.moderation_statuses.approved'),
                                'rejected' => __('admin.book.moderation_statuses.rejected'),
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('cover_image_url')
                            ->label(__('admin.book.cover_image_url'))
                            ->url()
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('admin.book.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('author')
                    ->label(__('admin.book.author'))
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label(__('admin.book.user'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label(__('admin.book.condition'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'excellent' => __('admin.book.conditions.excellent'),
                        'good' => __('admin.book.conditions.good'),
                        'fair' => __('admin.book.conditions.fair'),
                        'poor' => __('admin.book.conditions.poor'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('offer_type')
                    ->label(__('admin.book.offer_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sale' => __('admin.book.offer_types.sale'),
                        'exchange' => __('admin.book.offer_types.exchange'),
                        'donate' => __('admin.book.offer_types.donate'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'success',
                        'exchange' => 'info',
                        'donate' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('admin.book.price'))
                    ->money('SYP')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('admin.book.status'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => __('admin.book.statuses.available'),
                        'pending' => __('admin.book.statuses.pending'),
                        'sold' => __('admin.book.statuses.sold'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'pending' => 'warning',
                        'sold' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\BadgeColumn::make('moderation_status')
                    ->label(__('admin.book.moderation_status'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => __('admin.book.moderation_statuses.approved'),
                        'rejected' => __('admin.book.moderation_statuses.rejected'),
                        'pending' => __('admin.book.moderation_statuses.pending'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.book.date_added'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('moderation_status')
                    ->label(__('admin.book.moderation_status'))
                    ->options([
                        'pending' => __('admin.book.moderation_statuses.pending'),
                        'approved' => __('admin.book.moderation_statuses.approved'),
                        'rejected' => __('admin.book.moderation_statuses.rejected'),
                    ]),
                Tables\Filters\SelectFilter::make('condition')
                    ->label(__('admin.book.condition'))
                    ->options([
                        'excellent' => __('admin.book.conditions.excellent'),
                        'good' => __('admin.book.conditions.good'),
                        'fair' => __('admin.book.conditions.fair'),
                        'poor' => __('admin.book.conditions.poor'),
                    ]),
                Tables\Filters\SelectFilter::make('offer_type')
                    ->label(__('admin.book.offer_type'))
                    ->options([
                        'sale' => __('admin.book.offer_types.sale'),
                        'exchange' => __('admin.book.offer_types.exchange'),
                        'donate' => __('admin.book.offer_types.donate'),
                    ]),
            ])
            ->actions([
                Action::make('approveBook')
                    ->label(__('admin.book.approve_book'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.book.approve_book_heading'))
                    ->modalDescription(__('admin.book.approve_book_description'))
                    ->visible(fn (Book $record) => $record->moderation_status === 'pending')
                    ->action(function (Book $record) {
                        $record->update(['moderation_status' => 'approved']);
                        Notification::make()
                            ->title(__('admin.book.book_approved'))
                            ->success()
                            ->send();
                    }),
                Action::make('rejectBook')
                    ->label(__('admin.book.reject_book'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.book.reject_book_heading'))
                    ->modalDescription(__('admin.book.reject_book_description'))
                    ->visible(fn (Book $record) => $record->moderation_status === 'pending')
                    ->action(function (Book $record) {
                        $record->update(['moderation_status' => 'rejected']);
                        Notification::make()
                            ->title(__('admin.book.book_rejected'))
                            ->danger()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
