<?php

namespace App\Providers\Filament;

use App\Filament\Pages\CustomEditProfile;
use App\Livewire\ClientChart;
use App\Livewire\ClientTable;
use App\Livewire\StatsOverview;
use App\Livewire\Tenants\StatsOverviewTenant;
use App\Livewire\Tenants\UserChart;
use App\Livewire\Tenants\UserTable;
use App\Services\Tenant\TenantFilament;
use Config;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
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

class SupportPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('support')
            ->path('support')
            ->profile(CustomEditProfile::class)
            ->login()
            ->colors([
                'primary' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::make(),
                ClientTable::make(),
                ClientChart::make(),
                StatsOverviewTenant::make(),
                UserChart::make(),
                UserTable::make(),
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
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([

                    NavigationItem::make('Dashboard principal')
                        ->icon('heroicon-o-home')
                        ->url(fn () => Config::get('app.url').'/support'),

                    NavigationItem::make('Dashboard')
                        ->icon('heroicon-o-home')
                        ->url(fn (): string => Dashboard::getUrl())
                        ->visible(TenantFilament::availableForNavigation()),

                    NavigationItem::make('Clients')
                        ->icon('heroicon-o-user')
                        ->url(route('filament.support.resources.clients.index'))
                        ->visible(! TenantFilament::availableForNavigation()),

                    NavigationItem::make('Users')
                        ->label('Users')
                        ->icon('heroicon-o-users')
                        ->url(route('filament.support.resources.users.index')),

                    NavigationItem::make('Groups')
                        ->icon('heroicon-o-user-group')
                        ->url(route('filament.support.resources.groups.index'))
                        ->visible(TenantFilament::availableForNavigation()),

                    NavigationItem::make('Contacts')
                        ->label('Contacts')
                        ->icon('heroicon-o-phone')
                        ->url(route('filament.support.resources.contacts.index'))
                        ->visible(TenantFilament::availableForNavigation()),

                    NavigationItem::make('Histories')
                        ->label('Histories')
                        ->icon('heroicon-o-clock')
                        ->url(route('filament.support.resources.histories.index'))
                        ->visible(TenantFilament::availableForNavigation()),

                    NavigationItem::make('Link')
                        ->icon('heroicon-o-link')
                        ->url(route('filament.support.resources.links.index'))
                        ->visible(TenantFilament::availableForNavigation()),

                    NavigationItem::make('Themes')
                        ->label('Themes')
                        ->icon('heroicon-o-paint-brush')
                        ->url(route('filament.support.resources.themes.index'))
                        ->visible(TenantFilament::availableForNavigation()),

                ]);
            });
    }
}
