<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'إدارة المحتوى';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات التصنيف')
                    ->schema([
                        Forms\Components\TextInput::make('university_name')
                            ->label('اسم الجامعة')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('faculty_name')
                            ->label('اسم الكلية')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('department_name')
                            ->label('اسم القسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('study_year')
                            ->label('السنة الدراسية')
                            ->required()
                            ->maxLength(50),
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
                Tables\Columns\TextColumn::make('university_name')
                    ->label('الجامعة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('faculty_name')
                    ->label('الكلية')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department_name')
                    ->label('القسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('study_year')
                    ->label('السنة الدراسية')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
