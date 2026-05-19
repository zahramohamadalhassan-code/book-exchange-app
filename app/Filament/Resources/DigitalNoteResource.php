<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DigitalNoteResource\Pages;
use App\Models\DigitalNote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class DigitalNoteResource extends Resource
{
    protected static ?string $model = DigitalNote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): string
    {
        return __('admin.content_management');
    }

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('admin.note.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.note.model_label_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.note.section_data'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('admin.note.title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('admin.note.description'))
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('user_id')
                            ->label(__('admin.note.user'))
                            ->relationship('user', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('category_id')
                            ->label(__('admin.note.category'))
                            ->relationship('category', 'department_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\FileUpload::make('pdf_file_url')
                            ->label(__('admin.note.pdf_file'))
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('digital_notes')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('moderation_status')
                            ->label(__('admin.note.moderation_status'))
                            ->options([
                                'pending' => __('admin.note.moderation_statuses.pending'),
                                'approved' => __('admin.note.moderation_statuses.approved'),
                                'rejected' => __('admin.note.moderation_statuses.rejected'),
                            ])
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('admin.note.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label(__('admin.note.user'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.department_name')
                    ->label(__('admin.note.category')),
                Tables\Columns\BadgeColumn::make('moderation_status')
                    ->label(__('admin.note.moderation_status'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => __('admin.note.moderation_statuses.approved'),
                        'rejected' => __('admin.note.moderation_statuses.rejected'),
                        'pending' => __('admin.note.moderation_statuses.pending'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.note.created_at'))
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('moderation_status')
                    ->label(__('admin.note.moderation_status'))
                    ->options([
                        'pending' => __('admin.note.moderation_statuses.pending'),
                        'approved' => __('admin.note.moderation_statuses.approved'),
                        'rejected' => __('admin.note.moderation_statuses.rejected'),
                    ]),
            ])
            ->actions([
                Action::make('approveNote')
                    ->label(__('admin.note.approve_note'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.note.approve_note_heading'))
                    ->modalDescription(__('admin.note.approve_note_description'))
                    ->visible(fn (DigitalNote $record) => $record->moderation_status === 'pending')
                    ->action(function (DigitalNote $record) {
                        $record->update(['moderation_status' => 'approved']);
                        Notification::make()
                            ->title(__('admin.note.note_approved'))
                            ->success()
                            ->send();
                    }),
                Action::make('rejectNote')
                    ->label(__('admin.note.reject_note'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.note.reject_note_heading'))
                    ->modalDescription(__('admin.note.reject_note_description'))
                    ->visible(fn (DigitalNote $record) => $record->moderation_status === 'pending')
                    ->action(function (DigitalNote $record) {
                        $record->update(['moderation_status' => 'rejected']);
                        Notification::make()
                            ->title(__('admin.note.note_rejected'))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDigitalNotes::route('/'),
            'create' => Pages\CreateDigitalNote::route('/create'),
            'edit' => Pages\EditDigitalNote::route('/{record}/edit'),
        ];
    }
}
