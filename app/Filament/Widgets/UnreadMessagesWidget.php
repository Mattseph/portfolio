<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnreadMessagesWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $count = ContactMessage::unread()->count();
        return [
            Stat::make('Unread messages', $count)
                ->description($count > 0 ? 'Needs attention' : 'Inbox clear')
                ->color($count > 0 ? 'warning' : 'success')
                ->url(ContactMessageResource::getUrl('index')),
        ];
    }
}
