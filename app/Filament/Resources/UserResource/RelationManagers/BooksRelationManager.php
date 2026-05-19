<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BooksRelationManager extends RelationManager
{
    protected static string $relationship = 'books';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('admin.relation.books');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('admin.book.title'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author')
                    ->label(__('admin.book.author'))
                    ->required()
                    ->maxLength(255),
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('admin.book.title'))
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('author')
                    ->label(__('admin.book.author'))
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('condition')
                    ->label(__('admin.book.condition')),
                Tables\Columns\TextColumn::make('offer_type')
                    ->label(__('admin.book.offer_type')),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('admin.book.price'))
                    ->money('SYP')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('moderation_status')
                    ->label(__('admin.book.moderation_status'))
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
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
