<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public string $site_title;
    public string $tagline;
    public string $meta_description;
    public ?string $github_url;
    public ?string $linkedin_url;
    public ?string $twitter_url;
    public string $email;
    public string $footer_text;
    public bool $is_available_for_work;

    public static function group(): string { return 'site'; }
}
