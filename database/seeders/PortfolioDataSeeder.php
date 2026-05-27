<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Settings\AboutSettings;
use App\Settings\HomeSettings;
use App\Settings\SiteSettings;
use Illuminate\Database\Seeder;

class PortfolioDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedSkills();
        $this->seedExperiences();
        $this->seedServices();
        $this->seedProjects();
    }

    private function seedSettings(): void
    {
        $site = app(SiteSettings::class);
        $site->site_title = 'Matthew Bilaos | PHP/Laravel Developer';
        $site->tagline = 'PHP/Laravel Developer building scalable web applications.';
        $site->meta_description = 'Results-driven PHP/Laravel Developer with 2+ years of experience building scalable web applications with Laravel, Livewire, Vue.js, and Docker.';
        $site->github_url = null; // update via Filament admin
        $site->linkedin_url = null; // update via Filament admin
        $site->twitter_url = null;
        $site->email = 'matthewjoseph.bilaos@gmail.com';
        $site->footer_text = 'Built with Laravel & Livewire';
        $site->is_available_for_work = true;
        $site->save();

        $home = app(HomeSettings::class);
        $home->hero_headline = 'PHP/Laravel Developer who ships.';
        $home->hero_subheadline = 'I build scalable, maintainable Laravel systems — from RESTful APIs to full-stack Livewire apps.';
        $home->hero_cta_text = 'See my work';
        $home->hero_cta_url = '/projects';
        $home->save();

        $about = app(AboutSettings::class);
        $about->full_name = 'Matthew Joseph F. Bilaos';
        $about->bio = '<p>Results-driven PHP/Laravel Developer with 2+ years of experience building and deploying scalable web applications. Proficient in Laravel (MVC, Eloquent ORM, RESTful API, Livewire), Vue.js, MySQL, and Docker. Experienced in Agile/Scrum environments and client-facing communication. Skilled in writing clean, maintainable code and leveraging AI-assisted development workflows to accelerate delivery.</p>';
        $about->profile_image_path = null;
        $about->cv_pdf_path = null;
        $about->location = 'Banga, South Cotabato, Philippines';
        $about->years_experience = 2;
        $about->save();
    }

    private function seedSkills(): void
    {
        Skill::truncate();

        $skills = [
            // Languages
            ['name' => 'PHP',        'category' => 'language',  'proficiency' => 5, 'sort_order' => 1],
            ['name' => 'JavaScript', 'category' => 'language',  'proficiency' => 4, 'sort_order' => 2],
            ['name' => 'HTML',       'category' => 'language',  'proficiency' => 4, 'sort_order' => 3],
            ['name' => 'CSS',        'category' => 'language',  'proficiency' => 4, 'sort_order' => 4],

            // Frameworks
            ['name' => 'Laravel',      'category' => 'framework', 'proficiency' => 5, 'sort_order' => 1],
            ['name' => 'Livewire',     'category' => 'framework', 'proficiency' => 4, 'sort_order' => 2],
            ['name' => 'Vue.js',       'category' => 'framework', 'proficiency' => 4, 'sort_order' => 3],
            ['name' => 'Tailwind CSS', 'category' => 'framework', 'proficiency' => 4, 'sort_order' => 4],
            ['name' => 'Bootstrap',    'category' => 'framework', 'proficiency' => 3, 'sort_order' => 5],

            // Databases
            ['name' => 'MySQL',      'category' => 'database', 'proficiency' => 5, 'sort_order' => 1],
            ['name' => 'PostgreSQL', 'category' => 'database', 'proficiency' => 4, 'sort_order' => 2],

            // Tools
            ['name' => 'Git',           'category' => 'tool', 'proficiency' => 5, 'sort_order' => 1],
            ['name' => 'Docker',        'category' => 'tool', 'proficiency' => 4, 'sort_order' => 2],
            ['name' => 'Postman',       'category' => 'tool', 'proficiency' => 4, 'sort_order' => 3],
            ['name' => 'Jira',          'category' => 'tool', 'proficiency' => 3, 'sort_order' => 4],
            ['name' => 'Apache JMeter', 'category' => 'tool', 'proficiency' => 3, 'sort_order' => 5],

            // Platforms
            ['name' => 'GitHub',       'category' => 'platform', 'proficiency' => 5, 'sort_order' => 1],
            ['name' => 'Filament CMS', 'category' => 'platform', 'proficiency' => 5, 'sort_order' => 2],
            ['name' => 'DigitalOcean', 'category' => 'platform', 'proficiency' => 4, 'sort_order' => 3],
            ['name' => 'Laravel Nova', 'category' => 'platform', 'proficiency' => 3, 'sort_order' => 4],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }

    private function seedExperiences(): void
    {
        Experience::truncate();

        Experience::create([
            'company'    => 'Freelance',
            'role'       => 'PHP Developer',
            'location'   => 'Remote',
            'started_at' => '2026-01-01',
            'ended_at'   => null,
            'sort_order' => 1,
            'description' => '<ul>
<li>Architected and delivered end-to-end web applications using Laravel, Livewire, and MySQL — from requirements gathering and database design through production deployment on Digital Ocean.</li>
<li>Built feature-rich admin panels and content management systems using Filament CMS, enabling non-technical clients to independently manage application data and workflows.</li>
<li>Containerized applications with Docker for consistent local-to-production environments, deploying Laravel projects to Digital Ocean Droplets with reliable, repeatable release processes.</li>
<li>Translated client requirements into technical specifications and delivered fully functional web systems on schedule, covering full-stack PHP/Laravel development, database schema design, and user acceptance testing.</li>
</ul>',
        ]);

        Experience::create([
            'company'    => 'CoreProc Inc.',
            'role'       => 'Junior PHP Developer',
            'location'   => 'Bonifacio Global City, Taguig',
            'started_at' => '2024-11-01',
            'ended_at'   => null,
            'sort_order' => 2,
            'description' => '<ul>
<li>Engineered and maintained full-stack web features using Laravel (MVC, Eloquent ORM, Blade) and Vue.js, including seasonal promotions and dynamic wallet API integration serving live production users.</li>
<li>Designed and implemented secure RESTful API endpoints in Laravel, resolving critical security vulnerabilities and ensuring data integrity across a multi-tenant production environment.</li>
<li>Collaborated in Agile/Scrum sprints via Jira, directly communicating with clients to triage and resolve 10+ tickets per sprint, maintaining full project documentation in Confluence.</li>
<li>Identified and documented race condition risks in concurrent processes; proposed and implemented queue-based solutions using Laravel Jobs and Event Listeners to improve system reliability.</li>
<li>Assisted in Docker-based CI/CD deployment pipeline, supporting the Senior Developer in shipping production releases.</li>
<li>Leveraged AI-assisted development tools (Claude Code) for technical planning, code review, and automated test execution, reducing debugging cycle time and improving sprint throughput.</li>
</ul>',
        ]);

        Experience::create([
            'company'    => 'Mindanao State University — General Santos City',
            'role'       => 'BS Information Technology, Major in Database Systems',
            'location'   => 'General Santos City, Philippines',
            'started_at' => '2020-08-01',
            'ended_at'   => '2024-07-01',
            'sort_order' => 3,
            'description' => '<p>Graduated <strong>Cum Laude</strong>. Specialized in database systems design, normalization, and query optimization alongside full-stack web development coursework.</p>',
        ]);
    }

    private function seedServices(): void
    {
        Service::truncate();

        $services = [
            [
                'title'         => 'Laravel Web Application Development',
                'description'   => 'End-to-end Laravel application development — from database schema design and Eloquent models through Livewire-powered UI, queued jobs, and production deployment. Clean MVC architecture, tested with Pest.',
                'icon'          => 'heroicon-o-code-bracket',
                'starting_price' => 'Contact for quote',
                'sort_order'    => 1,
                'is_published'  => true,
            ],
            [
                'title'         => 'RESTful API Development & Integration',
                'description'   => 'Secure, well-documented RESTful APIs built with Laravel. Token authentication, rate limiting, versioning, and integration with third-party services. Tested with Postman and automated test suites.',
                'icon'          => 'heroicon-o-server',
                'starting_price' => 'Contact for quote',
                'sort_order'    => 2,
                'is_published'  => true,
            ],
            [
                'title'         => 'Filament CMS & Admin Panel',
                'description'   => 'Feature-rich admin dashboards and content management systems using Filament v3. Custom resources, widgets, settings pages, and role-based access — enabling non-technical users to manage their own data.',
                'icon'          => 'heroicon-o-rectangle-stack',
                'starting_price' => 'Contact for quote',
                'sort_order'    => 3,
                'is_published'  => true,
            ],
            [
                'title'         => 'Docker Containerization & Deployment',
                'description'   => 'Containerize existing or new Laravel applications with Docker Compose for consistent local-to-production parity. Deploy to DigitalOcean Droplets with repeatable, documented release processes.',
                'icon'          => 'heroicon-o-cube',
                'starting_price' => 'Contact for quote',
                'sort_order'    => 4,
                'is_published'  => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }

    private function seedProjects(): void
    {
        Project::truncate();

        $projects = [
            [
                'slug'         => 'developer-portfolio',
                'title'        => 'Developer Portfolio',
                'summary'      => 'This portfolio site — a Laravel 11 + Livewire 3 application with Filament CMS, GitHub activity integration, dark mode, and a contact form.',
                'role'         => 'Solo Developer',
                'started_at'   => '2026-04-01',
                'ended_at'     => null,
                'tech_stack'   => ['Laravel', 'Livewire', 'Filament', 'Tailwind CSS', 'Alpine.js', 'MySQL', 'Redis', 'Docker'],
                'problem'      => '<p>Needed a professional portfolio that reflected real backend engineering skills rather than a template site.</p>',
                'approach'     => '<p>Built with Laravel 11 full-page Livewire components, Filament v3 admin, Spatie Settings for content management, and hourly GitHub activity sync via a queued job.</p>',
                'challenges'   => '<p>Dark mode persistence across <code>wire:navigate</code> transitions required an Alpine.js + localStorage approach applied before the first paint to avoid flicker.</p>',
                'outcome'      => '<p>Fully self-managed portfolio with zero reliance on third-party CMSes, deployed on Docker with Redis queues and Mailpit for local mail.</p>',
                'repo_url'     => 'https://github.com/Mattseph/portfolio',
                'demo_url'     => null,
                'is_featured'  => true,
                'is_published' => true,
                'sort_order'   => 1,
            ],
            [
                'slug'         => 'drybrush-art-marketplace',
                'title'        => 'Drybrush Art Marketplace',
                'summary'      => 'Curated Laravel + Vue.js art e-commerce platform at CoreProc with artist profiles, full-text search, multi-gateway payments, and a live auction system.',
                'role'         => 'Junior PHP Developer',
                'started_at'   => '2024-11-01',
                'ended_at'     => null,
                'tech_stack'   => ['Laravel', 'Vue.js', 'MySQL', 'Redis', 'Meilisearch', 'Laravel Nova', 'Spatie Media Library', 'Laravel Horizon', 'Docker'],
                'problem'      => '<p>The platform needed custom admin tooling for artwork management and a new auction system with concurrent bid validation — all within a mature, long-running production codebase.</p>',
                'approach'     => '<p>Contributed to a domain-driven Laravel 9 codebase with custom Nova components for artwork tagging and image management. Implemented Meilisearch-backed full-text search across artworks, artists, and events, and helped ship the auction system with bid state management and outbid notifications.</p>',
                'challenges'   => '<p>Working in a 5-year-old production codebase required careful backward-compatible changes. Race conditions in concurrent bid submissions were mitigated using database-level locking and queue-based notification dispatch.</p>',
                'outcome'      => '<p>Actively maintained production platform serving Filipino art collectors. Successfully shipped the auctions feature with live bid management, automated outbid emails, and preview/bidding period state transitions.</p>',
                'repo_url'     => null,
                'demo_url'     => null,
                'is_featured'  => true,
                'is_published' => true,
                'sort_order'   => 2,
            ],
            [
                'slug'         => 'marlboro-ph-gamification',
                'title'        => 'Marlboro Philippines',
                'summary'      => 'Enterprise-grade Laravel 9 + Vue 3 loyalty and gamification platform for Philip Morris Philippines with UPC raffles, instant-win games, and real-time reward fulfillment.',
                'role'         => 'Junior PHP Developer',
                'started_at'   => '2024-11-01',
                'ended_at'     => null,
                'tech_stack'   => ['Laravel', 'Vue.js', 'Pinia', 'MySQL', 'Redis', 'Laravel Passport', 'Laravel Nova', 'Laravel Horizon', 'AWS S3', 'Pusher', 'Docker'],
                'problem'      => '<p>Philip Morris Philippines needed a scalable gamification platform to engage Marlboro customers across multiple interactive campaigns — UPC-entry raffles, instant-win games, trivia — while safely distributing prizes to thousands of concurrent users.</p>',
                'approach'     => '<p>Contributed to an action-based backend architecture with composable model traits (<code>CanWinLuckyPrizes</code>, <code>HasRaffleEntries</code>) and Pinia-driven Vue 3 frontend. Worked on integrating Gigya OAuth, Giftaway reward fulfillment, and Telerivet SMS OTP — each abstracted behind custom Laravel packages for testability.</p>',
                'challenges'   => '<p>Preventing race conditions in instant-win prize claims required cache-lock patterns (<code>UPC_INSTANT_PRIZE_CACHE_LOCK</code>) and ledger-based transaction tracking. Each external integration (6+ enterprise APIs) needed mock modes for development without hitting live services.</p>',
                'outcome'      => '<p>Long-running production platform (6+ years, 4,000+ commits) still receiving active updates, safely handling concurrent prize claims and real-time reward fulfillment across multiple Marlboro sub-brand campaigns.</p>',
                'repo_url'     => null,
                'demo_url'     => null,
                'is_featured'  => true,
                'is_published' => true,
                'sort_order'   => 3,
            ],
            [
                'slug'         => 'novalyf-mlm-platform',
                'title'        => 'NOVALYF MLM Investment Platform',
                'summary'      => 'Full-featured Laravel 12 MLM investment platform with binary tree commissions, 7-level unilevel networks, digital wallet, product shop, and a Filament v5 admin dashboard.',
                'role'         => 'Freelance Developer',
                'started_at'   => '2026-03-16',
                'ended_at'     => '2026-04-27',
                'tech_stack'   => ['Laravel', 'Livewire', 'Filament', 'Alpine.js', 'Tailwind CSS', 'MySQL', 'Docker', 'ApexCharts', 'Chart.js'],
                'problem'      => '<p>An MLM organization needed a single platform to manage member networks, multi-level commissions, investment plans, digital wallets, and product sales — replacing a fragmented manual process.</p>',
                'approach'     => '<p>Architected a service-layer backend (15+ domain services) covering binary tree placement, unilevel commission distribution, wallet transactions, and E-PIN activation. Built the member-facing portal with Livewire 4 and the full admin suite with Filament v5 resources organized by domain module.</p>',
                'challenges'   => '<p>The binary tree positioning algorithm required careful slot availability tracking to correctly distribute members across the network. Computing 7-level unilevel commissions with rank progression and volume thresholds demanded thorough domain modeling to keep calculations accurate and auditable.</p>',
                'outcome'      => '<p>Delivered a 249-commit production system in approximately 6 weeks covering the full MLM lifecycle — from member registration and E-PIN activation through binary/unilevel commission distribution, product fulfillment, and government-ready reporting.</p>',
                'repo_url'     => null,
                'demo_url'     => null,
                'is_featured'  => false,
                'is_published' => true,
                'sort_order'   => 4,
            ],
            [
                'slug'         => 'ph-payroll-saas',
                'title'        => 'Payroll SaaS Platform',
                'summary'      => 'Multi-tenant Laravel 12 SaaS for Philippine payroll with full statutory compliance (SSS, PhilHealth, Pag-IBIG, BIR), digital payslips, government reports, and an employee self-service portal.',
                'role'         => 'Developer',
                'started_at'   => '2026-03-05',
                'ended_at'     => null,
                'tech_stack'   => ['Laravel', 'Livewire', 'Filament', 'Tailwind CSS', 'MySQL', 'Docker', 'DomPDF', 'Spatie Media Library'],
                'problem'      => '<p>Payroll service providers needed a multi-tenant SaaS to manage Philippine payroll for multiple client companies simultaneously, with accurate statutory deductions and government-compliant reporting.</p>',
                'approach'     => '<p>Built modular Filament v5 resources organized by domain (Payroll, HR, Leave Management, Attendance), with dedicated service classes for each statutory computation. DomPDF generates digital payslips; CSV-based manual payroll upload handles edge-case payroll runs. The Livewire 4 employee portal provides self-service access to payslips, leave requests, and overtime submissions.</p>',
                'challenges'   => '<p>Implementing the annualized BIR withholding tax calculation correctly across different compensation structures was the most complex domain challenge. Handling the full matrix of overtime/premium pay combinations — ordinary, rest day, special holiday, and regular holiday each with optional night-shift differential overlay — required a carefully structured rate computation engine.</p>',
                'outcome'      => '<p>Production-ready multi-tenant payroll platform with complete Philippine statutory compliance, digital payslip generation, full government remittance reporting (SSS, PhilHealth, Pag-IBIG, BIR), 13th month pay processing, and an employee self-service portal with 2FA security.</p>',
                'repo_url'     => null,
                'demo_url'     => null,
                'is_featured'  => true,
                'is_published' => true,
                'sort_order'   => 5,
            ],
            [
                'slug'         => 'ybc-ecommerce',
                'title'        => 'YBC — Yemo Banana Crisps Store',
                'summary'      => 'Modern Laravel 13 + Livewire 4 e-commerce storefront for Yemo Banana Crisps with product catalog, bundle packs, session-based cart, order tracking, and a Filament v5 admin panel.',
                'role'         => 'Freelance Developer',
                'started_at'   => '2026-04-20',
                'ended_at'     => '2026-05-04',
                'tech_stack'   => ['Laravel', 'Livewire', 'Filament', 'Flux UI', 'Tailwind CSS', 'MySQL', 'Redis', 'Docker'],
                'problem'      => '<p>Yemo Banana Crisps needed an e-commerce platform to sell products online with support for bundle packs and a simple order tracking system for customers.</p>',
                'approach'     => '<p>Used full-page Livewire 4 components throughout — no traditional controllers. Built a session-based cart supporting both individual products and bundles (Bundle → BundleItem → OrderItem). Filament v5 handles complete store management including product images, bundle configuration, order status, testimonials, and newsletter subscribers. Auto-generated track IDs (<code>YBC-YYYYMMDD-XXXX</code>) let customers check order status without an account.</p>',
                'challenges'   => '<p>Designing the bundle architecture to integrate cleanly with the cart and checkout flow required careful relationship modeling. A cached site settings system — auto-invalidated on any admin write — was built to avoid unnecessary DB hits while keeping the admin experience instant.</p>',
                'outcome'      => '<p>Complete storefront delivered in 2 weeks (38 commits) with product management, bundle packs, order tracking, newsletter subscriptions, and customer testimonials — all self-managed via the Filament admin panel.</p>',
                'repo_url'     => 'https://github.com/Mattseph/ybc-web',
                'demo_url'     => null,
                'is_featured'  => false,
                'is_published' => true,
                'sort_order'   => 6,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
