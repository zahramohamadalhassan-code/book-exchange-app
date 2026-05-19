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

    public static function getNavigationGroup(): string
    {
        return __('admin.user_management');
    }

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('admin.user.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.user.model_label_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.user.section_data'))
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->label(__('admin.user.full_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('admin.user.email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label(__('admin.user.password'))
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('university_id')
                            ->label(__('admin.user.university_id'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Forms\Components\TextInput::make('phone_number')
                            ->label(__('admin.user.phone_number'))
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('role_id')
                            ->label(__('admin.user.role'))
                            ->relationship('role', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_banned')
                            ->label(__('admin.user.is_banned'))
                            ->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('admin.user.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('admin.user.full_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('admin.user.email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('university_id')
                    ->label(__('admin.user.university_id'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->label(__('admin.user.role'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Admin' => 'danger',
                        'Student' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_banned')
                    ->label(__('admin.user.is_banned'))
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.user.date_registered'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_banned')
                    ->label(__('admin.user.banned_filter'))
                    ->placeholder(__('admin.user.all'))
                    ->trueLabel(__('admin.user.banned_only'))
                    ->falseLabel(__('admin.user.active_only')),
            ])
            ->actions([
                Action::make('toggleBan')
                    ->label(fn (User $record) => $record->is_banned ? __('admin.user.toggle_unban') : __('admin.user.toggle_ban'))
                    ->icon(fn (User $record) => $record->is_banned ? 'heroicon-o-lock-open' : 'heroicon-o-no-symbol')
                    ->color(fn (User $record) => $record->is_banned ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => $record->is_banned ? __('admin.user.unban_heading') : __('admin.user.ban_heading'))
                    ->modalDescription(fn (User $record) => $record->is_banned ? __('admin.user.unban_description') : __('admin.user.ban_description'))
                    ->action(function (User $record) {
                        $record->update(['is_banned' => ! $record->is_banned]);
                        Notification::make()
                            ->title($record->is_banned ? __('admin.user.user_banned') : __('admin.user.user_unbanned'))
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
