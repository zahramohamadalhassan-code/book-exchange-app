<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Models\Rating;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfolistSection;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getNavigationGroup(): string
    {
        return __('admin.monitoring');
    }

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('admin.rating.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.rating.model_label_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('admin.rating.section_data'))
                    ->schema([
                        TextInput::make('transaction_id')
                            ->label(__('admin.rating.transaction_id'))
                            ->disabled(),
                        TextInput::make('reviewer.full_name')
                            ->label(__('admin.rating.reviewer'))
                            ->disabled(),
                        TextInput::make('reviewedUser.full_name')
                            ->label(__('admin.rating.reviewed_user'))
                            ->disabled(),
                        TextInput::make('stars')
                            ->label(__('admin.rating.stars'))
                            ->numeric()
                            ->disabled(),
                        Textarea::make('comment')
                            ->label(__('admin.rating.comment'))
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make(__('admin.rating.section_data'))
                    ->schema([
                        TextEntry::make('transaction_id')
                            ->label(__('admin.rating.transaction_id')),
                        TextEntry::make('reviewer.full_name')
                            ->label(__('admin.rating.reviewer')),
                        TextEntry::make('reviewedUser.full_name')
                            ->label(__('admin.rating.reviewed_user')),
                        TextEntry::make('stars')
                            ->label(__('admin.rating.stars'))
                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)),
                        TextEntry::make('comment')
                            ->label(__('admin.rating.comment'))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label(__('admin.rating.transaction_id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewer.full_name')
                    ->label(__('admin.rating.reviewer'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('reviewedUser.full_name')
                    ->label(__('admin.rating.reviewed_user'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('stars')
                    ->label(__('admin.rating.stars'))
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label(__('admin.rating.comment'))
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.rating.date'))
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
