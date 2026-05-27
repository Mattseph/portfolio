<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AboutSettings extends Settings
{
    public string $full_name;
    public string $bio;
    public ?string $profile_image_path;
    public ?string $cv_pdf_path;
    public ?string $location;
    public ?int $years_experience;

    public static function group(): string { return 'about'; }
}
