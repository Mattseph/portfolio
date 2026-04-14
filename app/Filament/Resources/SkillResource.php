<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillResource\Pages;
use App\Models\Skill;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(80),
            Select::make('category')->required()->options([
                'language' => 'Language',
                'framework' => 'Framework',
                'database' => 'Database',
                'tool' => 'Tool',
                'platform' => 'Platform',
            ]),
            TextInput::make('icon')->helperText('devicon class, e.g., devicon-laravel-plain')->maxLength(100),
            TextInput::make('proficiency')->numeric()->minValue(1)->maxValue(5)->default(3),
            TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->groups(['category'])
            ->defaultGroup('category')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('category'),
                TextColumn::make('proficiency'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit' => Pages\EditSkill::route('/{record}/edit'),
        ];
    }
}
