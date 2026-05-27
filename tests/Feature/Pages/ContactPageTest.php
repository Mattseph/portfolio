<?php

it('renders the contact page', function () {
    $this->get('/contact')->assertOk()->assertSee('Get in touch');
});

it('shows github link when configured', function () {
    $site = app(\App\Settings\SiteSettings::class);
    $site->github_url = 'https://github.com/testuser';
    $site->save();

    $this->get('/contact')->assertSee('https://github.com/testuser');
});

it('shows linkedin link when configured', function () {
    $site = app(\App\Settings\SiteSettings::class);
    $site->linkedin_url = 'https://linkedin.com/in/testuser';
    $site->save();

    $this->get('/contact')->assertSee('https://linkedin.com/in/testuser');
});

it('shows twitter link when configured', function () {
    $site = app(\App\Settings\SiteSettings::class);
    $site->twitter_url = 'https://twitter.com/testuser';
    $site->save();

    $this->get('/contact')->assertSee('https://twitter.com/testuser');
});
