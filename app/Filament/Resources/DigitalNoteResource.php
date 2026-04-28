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

    protected static ?string $navigationGroup = 'إدارة المحتوى';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الملاحظات الرقمية')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->maxLength(1000)
                            ->columnSpanFull(),
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
                        Forms\Components\FileUpload::make('pdf_file_url')
                            ->label('ملف PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('digital_notes')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('moderation_status')
                            ->label('حالة المراجعة')
                            ->options([
                                'pending' => 'معلق',
                                'approved' => 'مقبول',
                                'rejected' => 'مرفوض',
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
                    ->label('العنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('المستخدم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.department_name')
                    ->label('القسم'),
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
            ])
            ->actions([
                Action::make('approveNote')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('قبول الملاحظة')
                    ->modalDescription('هل أنت متأكد من قبول هذه الملاحظة؟')
                    ->visible(fn (DigitalNote $record) => $record->moderation_status === 'pending')
                    ->action(function (DigitalNote $record) {
                        $record->update(['moderation_status' => 'approved']);
                        Notification::make()
                            ->title('تم قبول الملاحظة')
                            ->success()
                            ->send();
                    }),
                Action::make('rejectNote')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('رفض الملاحظة')
                    ->modalDescription('هل أنت متأكد من رفض هذه الملاحظة؟')
                    ->visible(fn (DigitalNote $record) => $record->moderation_status === 'pending')
                    ->action(function (DigitalNote $record) {
                        $record->update(['moderation_status' => 'rejected']);
                        Notification::make()
                            ->title('تم رفض الملاحظة')
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
