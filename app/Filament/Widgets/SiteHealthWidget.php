<?php

namespace App\Filament\Widgets;

use App\Models\GithubActivityCache;
use App\Settings\SiteSettings;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteHealthWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $snap = GithubActivityCache::latestSnapshot();
        $lastSync = $snap?->fetched_at?->diffForHumans() ?? 'never';

        return [
            Stat::make('Available for work', app(SiteSettings::class)->is_available_for_work ? 'Yes' : 'No'),
            Stat::make('Last GitHub sync', $lastSync),
        ];
    }
}
