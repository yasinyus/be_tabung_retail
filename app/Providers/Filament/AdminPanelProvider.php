<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()                    // ✅ ENABLE LOGIN
            ->authGuard('web')          // ✅ SET AUTH GUARD
            ->brandLogo(asset('img/logo.png'))
            ->brandLogoHeight('4rem')
            ->brandName('Admin Tabung Retail')
            ->renderHook(
                'panels::auth.login.form.before',
                fn () => '<style>
                    .fi-simple-layout .fi-logo img {
                        border-radius: 50% !important;
                        object-fit: cover !important;
                        aspect-ratio: 1 !important;
                        width: 6rem !important;
                        height: 6rem !important;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
                        margin-bottom: 1rem !important;
                    }
                    .fi-simple-layout .fi-logo {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        margin-bottom: 2rem !important;
                    }
                    .fi-simple-main {
                        max-width: 400px !important;
                    }
                </style>'
            )
            ->renderHook(
                'panels::body.end',
                fn () => view('filament.custom-styles')
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->resources([
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\Tabungs\TabungResource::class,
                \App\Filament\Resources\TabungActivityResource::class,
                \App\Filament\Resources\VolumeTabungResource::class,
                \App\Filament\Resources\Armadas\ArmadaResource::class,
                \App\Filament\Resources\Pelanggans\PelangganResource::class,
                \App\Filament\Resources\Gudangs\GudangResource::class,
                \App\Filament\Resources\Deposits\DepositResource::class,
                \App\Filament\Resources\Audits\AuditResource::class,
                \App\Filament\Resources\Transactions\TransactionResource::class,
                \App\Filament\Resources\Tagihans\TagihanResource::class,
            ])
            ->pages([
                Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \Filament\Widgets\AccountWidget::class,
                // Temporarily disabled problematic widgets that cause childNodes errors:
                // \App\Filament\Widgets\TabungActivityJavaScript::class,
                // \App\Filament\Widgets\PelangganChart::class,
                // \App\Filament\Widgets\MonthlyRegistrationsChart::class,
                // \App\Filament\Widgets\TabungQrChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\PreventDoubleLogin::class, // ✅ RE-ENABLED PREVENT DOUBLE LOGIN
            ])
            ->authMiddleware([
                Authenticate::class,     // ✅ ENABLE AUTH MIDDLEWARE
            ]);

    }
}