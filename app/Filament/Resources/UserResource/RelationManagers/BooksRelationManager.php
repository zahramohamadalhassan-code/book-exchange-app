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

    protected static ?string $title = 'الكتب';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('العنوان')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author')
                    ->label('المؤلف')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'department_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('condition')
                    ->label('حالة الكتاب')
                    ->options([
                        'excellent' => 'ممتاز',
                        'good' => 'جيد',
                        'fair' => 'مقبول',
                        'poor' => 'سيء',
                    ])
                    ->required(),
                Forms\Components\Select::make('offer_type')
                    ->label('نوع العرض')
                    ->options([
                        'sale' => 'بيع',
                        'exchange' => 'تبادل',
                        'donate' => 'تبرع',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('السعر')
                    ->numeric()
                    ->prefix('SYP')
                    ->visible(fn (callable $get) => $get('offer_type') === 'sale'),
                Forms\Components\Select::make('status')
                    ->label('الحالة')
                    ->options([
                        'available' => 'متاح',
                        'pending' => 'قيد الانتظار',
                        'sold' => 'مباع',
                    ])
                    ->required(),
                Forms\Components\Select::make('moderation_status')
                    ->label('حالة المراجعة')
                    ->options([
                        'pending' => 'معلق',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('cover_image_url')
                    ->label('رابط صورة الغلاف')
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
                    ->label('العنوان')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('author')
                    ->label('المؤلف')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('condition')
                    ->label('الحالة'),
                Tables\Columns\TextColumn::make('offer_type')
                    ->label('نوع العرض'),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SYP')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('moderation_status')
                    ->label('حالة المراجعة')
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
