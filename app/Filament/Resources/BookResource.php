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

    protected static ?string $navigationGroup = 'إدارة المحتوى';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الكتاب')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author')
                            ->label('المؤلف')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('user_id')
                            ->label('المستخدم')
                            ->relationship('user', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('author')
                    ->label('المؤلف')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('المستخدم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('حالة الكتاب')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('offer_type')
                    ->label('نوع العرض')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sale' => 'success',
                        'exchange' => 'info',
                        'donate' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('SYP')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'pending' => 'warning',
                        'sold' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\BadgeColumn::make('moderation_status')
                    ->label('حالة المراجعة')
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('moderation_status')
                    ->label('حالة المراجعة')
                    ->options([
                        'pending' => 'معلق',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ]),
                Tables\Filters\SelectFilter::make('condition')
                    ->label('حالة الكتاب')
                    ->options([
                        'excellent' => 'ممتاز',
                        'good' => 'جيد',
                        'fair' => 'مقبول',
                        'poor' => 'سيء',
                    ]),
                Tables\Filters\SelectFilter::make('offer_type')
                    ->label('نوع العرض')
                    ->options([
                        'sale' => 'بيع',
                        'exchange' => 'تبادل',
                        'donate' => 'تبرع',
                    ]),
            ])
            ->actions([
                Action::make('approveBook')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('قبول الكتاب')
                    ->modalDescription('هل أنت متأكد من قبول هذا الكتاب؟')
                    ->visible(fn (Book $record) => $record->moderation_status === 'pending')
                    ->action(function (Book $record) {
                        $record->update(['moderation_status' => 'approved']);
                        Notification::make()
                            ->title('تم قبول الكتاب')
                            ->success()
                            ->send();
                    }),
                Action::make('rejectBook')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('رفض الكتاب')
                    ->modalDescription('هل أنت متأكد من رفض هذا الكتاب؟')
                    ->visible(fn (Book $record) => $record->moderation_status === 'pending')
                    ->action(function (Book $record) {
                        $record->update(['moderation_status' => 'rejected']);
                        Notification::make()
                            ->title('تم رفض الكتاب')
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
