<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomeSettings extends Settings
{
    public string $hero_headline;
    public string $hero_subheadline;
    public string $hero_cta_text;
    public string $hero_cta_url;

    public static function group(): string { return 'home'; }
}
