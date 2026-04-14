<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceResource\Pages;
use App\Models\Experience;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('company')->required()->maxLength(120),
            TextInput::make('role')->required()->maxLength(120),
            TextInput::make('location')->maxLength(120),
            DatePicker::make('started_at')->required(),
            Toggle::make('currently_here')->dehydrated(false)->live()
                ->afterStateUpdated(fn ($state, $set) => $state ? $set('ended_at', null) : null),
            DatePicker::make('ended_at')->visible(fn ($get) => ! $get('currently_here')),
            RichEditor::make('description')->toolbarButtons(['bold', 'italic', 'bulletList', 'link'])->columnSpanFull(),
            FileUpload::make('logo_path')->image()->disk('public')->directory('experiences/logos')->maxSize(5120)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
            TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('company')->searchable(),
                TextColumn::make('role'),
                TextColumn::make('started_at')->date(),
                TextColumn::make('ended_at')->date()->placeholder('Present'),
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
            'index' => Pages\ListExperiences::route('/'),
            'create' => Pages\CreateExperience::route('/create'),
            'edit' => Pages\EditExperience::route('/{record}/edit'),
        ];
    }
}
