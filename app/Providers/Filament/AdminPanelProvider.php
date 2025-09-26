<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use App\Filament\Widgets\RoleStatsWidget;
use App\Filament\Widgets\SystemOverviewWidget;
use App\Filament\Widgets\RecentActivitiesWidget;
use App\Filament\Widgets\SalesChartWidget;
use App\Filament\Widgets\PurchaseChartWidget;
use App\Filament\Widgets\TopProductsWidget;
use App\Filament\Widgets\TopCustomersWidget;
use App\Filament\Widgets\InventoryStatusWidget;
use App\Filament\Widgets\FinancialSummaryWidget;

use App\Filament\Widgets\SystemHealthWidget;
use Hasnayeen\Themes\ThemesPlugin;
use Hasnayeen\Themes\Http\Middleware\SetTheme;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //SystemOverviewWidget::class,
                //RecentActivitiesWidget::class,
                //SalesChartWidget::class,
                //PurchaseChartWidget::class,
                //TopProductsWidget::class,
                //TopCustomersWidget::class,
                //InventoryStatusWidget::class,
                //FinancialSummaryWidget::class,
                //SystemHealthWidget::class,
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
                SetTheme::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                // Enable all Breezy features for comprehensive user management
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        navigationGroup: 'Settings',
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->enableTwoFactorAuthentication(force: false)
                    ->enableSanctumTokens()
                    ->enableBrowserSessions(),
                ThemesPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
