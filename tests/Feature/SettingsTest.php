<?php

use App\Settings\SiteSettings;

it('loads site settings with seeded defaults', function () {
    $settings = app(SiteSettings::class);
    expect($settings->site_title)->toBe('Portfolio')
        ->and($settings->is_available_for_work)->toBeTrue();
});
