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

    public static function getNavigationGroup(): string
    {
        return __('admin.content_management');
    }

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('admin.category.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.category.model_label_plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.category.section_data'))
                    ->schema([
                        Forms\Components\TextInput::make('university_name')
                            ->label(__('admin.category.university_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('faculty_name')
                            ->label(__('admin.category.faculty_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('department_name')
                            ->label(__('admin.category.department_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('study_year')
                            ->label(__('admin.category.study_year'))
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
                    ->label(__('admin.category.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('university_name')
                    ->label(__('admin.category.university'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('faculty_name')
                    ->label(__('admin.category.faculty'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('department_name')
                    ->label(__('admin.category.department'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('study_year')
                    ->label(__('admin.category.study_year'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.category.created_at'))
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
