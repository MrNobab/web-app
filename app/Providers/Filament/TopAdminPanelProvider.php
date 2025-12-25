<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TopAdminPanelProvider extends PanelProvider
{
    // 👇 FIXED: Changed 'PanelBuilder' to 'Panel'
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('top_admin')
            ->path('top-admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            // Ensure these paths match where you are about to create the files
            ->discoverResources(in: app_path('Filament/TopAdmin/Resources'), for: 'App\\Filament\\TopAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/TopAdmin/Pages'), for: 'App\\Filament\\TopAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TopAdmin/Widgets'), for: 'App\\Filament\\TopAdmin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}