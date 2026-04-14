<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('site.site_title', 'Portfolio');
        $this->migrator->add('site.tagline', 'Backend engineer building reliable Laravel systems.');
        $this->migrator->add('site.meta_description', 'Laravel/PHP backend engineer portfolio.');
        $this->migrator->add('site.github_url', null);
        $this->migrator->add('site.linkedin_url', null);
        $this->migrator->add('site.twitter_url', null);
        $this->migrator->add('site.email', 'hello@example.com');
        $this->migrator->add('site.footer_text', 'Built with Laravel');
        $this->migrator->add('site.is_available_for_work', true);

        $this->migrator->add('home.hero_headline', 'Backend engineer who ships.');
        $this->migrator->add('home.hero_subheadline', 'I build reliable Laravel systems for teams that need things to work.');
        $this->migrator->add('home.hero_cta_text', 'See selected work');
        $this->migrator->add('home.hero_cta_url', '/projects');

        $this->migrator->add('about.bio', '<p>Backend engineer with a focus on Laravel, queues, and data integrity.</p>');
        $this->migrator->add('about.profile_image_path', null);
        $this->migrator->add('about.cv_pdf_path', null);
        $this->migrator->add('about.location', 'Remote');
        $this->migrator->add('about.years_experience', 5);
    }
};
