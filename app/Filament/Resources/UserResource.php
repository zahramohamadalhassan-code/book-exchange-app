<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\BooksRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\TransactionsRelationManager;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'إدارة المستخدمين';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المستخدم')
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->label('الاسم الكامل')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('university_id')
                            ->label('الرقم الجامعي')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('phone_number')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('role_id')
                            ->label('الدور')
                            ->relationship('role', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_banned')
                            ->label('محظور')
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('الاسم الكامل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('university_id')
                    ->label('الرقم الجامعي')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->label('الدور')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Admin' => 'danger',
                        'Student' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_banned')
                    ->label('محظور')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_banned')
                    ->label('محظور')
                    ->placeholder('الكل')
                    ->trueLabel('محظورين فقط')
                    ->falseLabel('غير محظورين فقط'),
            ])
            ->actions([
                Action::make('toggleBan')
                    ->label(fn (User $record) => $record->is_banned ? 'إلغاء الحظر' : 'حظر')
                    ->icon(fn (User $record) => $record->is_banned ? 'heroicon-o-lock-open' : 'heroicon-o-no-symbol')
                    ->color(fn (User $record) => $record->is_banned ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => $record->is_banned ? 'إلغاء حظر المستخدم' : 'حظر المستخدم')
                    ->modalDescription(fn (User $record) => $record->is_banned ? 'هل أنت متأكد من إلغاء حظر هذا المستخدم؟' : 'هل أنت متأكد من حظر هذا المستخدم؟')
                    ->action(function (User $record) {
                        $record->update(['is_banned' => ! $record->is_banned]);
                        Notification::make()
                            ->title($record->is_banned ? 'تم حظر المستخدم' : 'تم إلغاء حظر المستخدم')
                            ->success()
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
            BooksRelationManager::class,
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
