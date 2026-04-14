<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->disabled(),
            TextInput::make('email')->disabled(),
            TextInput::make('subject')->disabled(),
            Textarea::make('message')->disabled()->rows(8)->columnSpanFull(),
            TextInput::make('ip_address')->disabled(),
            DateTimePicker::make('created_at')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')->boolean()->label('Read'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('subject')->limit(40)->searchable(),
                TextColumn::make('created_at')->since(),
            ])
            ->filters([
                TernaryFilter::make('is_read')->label('Read status'),
            ])
            ->actions([
                Action::make('view')->icon('heroicon-o-eye')
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record])),
                Action::make('markRead')->icon('heroicon-o-check')
                    ->visible(fn ($record) => ! $record->is_read)
                    ->action(fn ($record) => $record->update(['is_read' => true])),
                Action::make('reply')->icon('heroicon-o-envelope')
                    ->url(fn ($record) => 'mailto:' . $record->email)->openUrlInNewTab(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::unread()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
