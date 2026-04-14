<?php

namespace App\Filament\Pages;

use App\Settings\AboutSettings;
use App\Settings\HomeSettings;
use App\Settings\SiteSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.settings';
    protected static ?int $navigationSort = 99;

    public array $data = [];

    public function mount(): void
    {
        $site = app(SiteSettings::class);
        $home = app(HomeSettings::class);
        $about = app(AboutSettings::class);

        $this->form->fill([
            'site' => $site->toArray(),
            'home' => $home->toArray(),
            'about' => $about->toArray(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make()->tabs([
                Tabs\Tab::make('Site')->schema([
                    TextInput::make('site.site_title')->required(),
                    TextInput::make('site.tagline')->required(),
                    Textarea::make('site.meta_description')->required(),
                    TextInput::make('site.email')->email()->required(),
                    TextInput::make('site.github_url')->url(),
                    TextInput::make('site.linkedin_url')->url(),
                    TextInput::make('site.twitter_url')->url(),
                    TextInput::make('site.footer_text'),
                    Toggle::make('site.is_available_for_work'),
                ]),
                Tabs\Tab::make('Home')->schema([
                    TextInput::make('home.hero_headline')->required(),
                    Textarea::make('home.hero_subheadline')->required(),
                    TextInput::make('home.hero_cta_text')->required(),
                    TextInput::make('home.hero_cta_url')->required(),
                ]),
                Tabs\Tab::make('About')->schema([
                    RichEditor::make('about.bio')->columnSpanFull(),
                    FileUpload::make('about.profile_image_path')->image()->disk('public')->directory('about')->maxSize(5120),
                    FileUpload::make('about.cv_pdf_path')->acceptedFileTypes(['application/pdf'])->disk('public')->directory('about')->maxSize(10240),
                    TextInput::make('about.location'),
                    TextInput::make('about.years_experience')->numeric(),
                ]),
            ])->columnSpanFull(),
        ])->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $site = app(SiteSettings::class);
        foreach ($state['site'] as $k => $v) { $site->{$k} = $v; }
        $site->save();

        $home = app(HomeSettings::class);
        foreach ($state['home'] as $k => $v) { $home->{$k} = $v; }
        $home->save();

        $about = app(AboutSettings::class);
        foreach ($state['about'] as $k => $v) { $about->{$k} = $v; }
        $about->save();

        Notification::make()->title('Settings saved')->success()->send();
    }
}
