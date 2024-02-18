<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Setting;
use App\Filament\Admin\Resources\OrderResource;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\FontProviders\GoogleFontProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Blade;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use RyanChandler\FilamentMinimalTabs\MinimalTabsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => '#003159',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications(true)
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Setting::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
            ])
            ->sidebarWidth('17rem')
            ->font('Noto Sans Arabic', provider: GoogleFontProvider::class)
            ->plugins([
                MinimalTabsPlugin::make(),
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                    FilamentApexChartsPlugin::make()
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandLogo(asset('bazar-logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('favicon.png '))
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
            ])->authGuard('admin')
//            ->renderHook('panels::footer', fn(): View => view('filament.hooks.footer'))
            ->renderHook('panels::global-search.before', hook: function (): string {
                return Blade::render('@livewire(\'clear-cache-button\')');
            })->spa();
    }
}
