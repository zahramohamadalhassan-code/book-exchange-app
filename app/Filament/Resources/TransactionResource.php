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
use Filament\Infolists\Components\Grid;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'المراقبة';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('بيانات المعاملة')
                    ->schema([
                        Select::make('book_id')
                            ->label('الكتاب')
                            ->relationship('book', 'title')
                            ->disabled(),
                        Select::make('requester_id')
                            ->label('الطالب')
                            ->relationship('requester', 'full_name')
                            ->disabled(),
                        Select::make('owner_id')
                            ->label('المالك')
                            ->relationship('owner', 'full_name')
                            ->disabled(),
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'pending' => 'معلق',
                                'accepted' => 'مقبول',
                                'rejected' => 'مرفوض',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                            ])
                            ->disabled(),
                        DatePicker::make('meeting_date')
                            ->label('تاريخ اللقاء')
                            ->disabled(),
                        TextInput::make('meeting_time')
                            ->label('وقت اللقاء')
                            ->disabled(),
                        TextInput::make('meeting_location')
                            ->label('مكان اللقاء')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('بيانات المعاملة')
                    ->schema([
                        TextEntry::make('book.title')
                            ->label('الكتاب'),
                        TextEntry::make('requester.full_name')
                            ->label('الطالب'),
                        TextEntry::make('owner.full_name')
                            ->label('المالك'),
                        TextEntry::make('status')
                            ->label('الحالة')
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
                                'pending' => 'معلق',
                                'accepted' => 'مقبول',
                                'rejected' => 'مرفوض',
                                'completed' => 'مكتمل',
                                'cancelled' => 'ملغي',
                                default => $state,
                            }),
                        TextEntry::make('meeting_date')
                            ->label('تاريخ اللقاء')
                            ->date('Y-m-d'),
                        TextEntry::make('meeting_time')
                            ->label('وقت اللقاء'),
                        TextEntry::make('meeting_location')
                            ->label('مكان اللقاء'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('الكتاب')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('requester.full_name')
                    ->label('الطالب')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner.full_name')
                    ->label('المالك')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'success',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('meeting_date')
                    ->label('تاريخ اللقاء')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending' => 'معلق',
                        'accepted' => 'مقبول',
                        'rejected' => 'مرفوض',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغي',
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
