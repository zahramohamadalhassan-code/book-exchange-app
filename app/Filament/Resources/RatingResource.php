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

    protected static ?string $navigationGroup = 'المراقبة';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('بيانات التقييم')
                    ->schema([
                        TextInput::make('transaction_id')
                            ->label('رقم المعاملة')
                            ->disabled(),
                        TextInput::make('reviewer.full_name')
                            ->label('المقيّم')
                            ->disabled(),
                        TextInput::make('reviewedUser.full_name')
                            ->label('المقيَّم')
                            ->disabled(),
                        TextInput::make('stars')
                            ->label('النجوم')
                            ->numeric()
                            ->disabled(),
                        Textarea::make('comment')
                            ->label('التعليق')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('بيانات التقييم')
                    ->schema([
                        TextEntry::make('transaction_id')
                            ->label('رقم المعاملة'),
                        TextEntry::make('reviewer.full_name')
                            ->label('المقيّم'),
                        TextEntry::make('reviewedUser.full_name')
                            ->label('المقيَّم'),
                        TextEntry::make('stars')
                            ->label('النجوم')
                            ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state)),
                        TextEntry::make('comment')
                            ->label('التعليق')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('رقم المعاملة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewer.full_name')
                    ->label('المقيّم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reviewedUser.full_name')
                    ->label('المقيَّم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stars')
                    ->label('النجوم')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('التعليق')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التقييم')
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
