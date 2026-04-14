<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make()->tabs([
                Tabs\Tab::make('Basic')->schema([
                    TextInput::make('title')->required()->maxLength(150)->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(160),
                    TextInput::make('summary')->required()->maxLength(255),
                    TextInput::make('role')->maxLength(100),
                    DatePicker::make('started_at'),
                    DatePicker::make('ended_at'),
                    TagsInput::make('tech_stack')->placeholder('Laravel, MySQL, Redis'),
                    Toggle::make('is_featured'),
                    Toggle::make('is_published'),
                    TextInput::make('sort_order')->numeric()->default(0),
                ]),
                Tabs\Tab::make('Content')->schema([
                    RichEditor::make('problem')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
                    RichEditor::make('approach')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
                    RichEditor::make('challenges')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
                    RichEditor::make('outcome')->toolbarButtons(['bold', 'italic', 'bulletList', 'link', 'h2', 'h3'])->columnSpanFull(),
                ]),
                Tabs\Tab::make('Media')->schema([
                    FileUpload::make('cover_image_path')->image()->disk('public')->directory('projects/covers')->maxSize(5120)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                    FileUpload::make('architecture_image_path')->image()->disk('public')->directory('projects/architecture')->maxSize(5120)->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                ]),
                Tabs\Tab::make('Links')->schema([
                    TextInput::make('repo_url')->url()->maxLength(255),
                    TextInput::make('demo_url')->url()->maxLength(255),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                IconColumn::make('is_featured')->boolean()->label('Featured'),
                IconColumn::make('is_published')->boolean()->label('Published'),
                TextColumn::make('updated_at')->since()->label('Updated'),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
