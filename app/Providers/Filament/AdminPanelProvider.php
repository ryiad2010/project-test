<?php

namespace App\Providers\Filament;

use App\Filament\AvatarProviders\BoringAvatarsProvider;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Settings;
use App\Filament\Resources\PostResource;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\Post;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Pages;
use Filament\Pages\Dashboard;
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
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Ryiad\FilamentToolkit\Resources\UserResource;
use Ryiad\FilamentToolkit\Toolkit;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->defaultAvatarProvider(BoringAvatarsProvider::class)
            ->maxContentWidth(MaxWidth::ScreenTwoExtraLarge)
            ->login()
            ->brandLogo(fn() => view('filament.admin.logo'))
            ->favicon(asset('images/favicon.png'))
            ->brandLogoHeight('30px')
            // ->brandName('Ryiad Demo')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->font('Poppins')
            //  ->viteTheme('resources/css/filament/admin/theme.css')
            ->spa()
            ->unsavedChangesAlerts()
            ->assets([
                Css::make('admin-custom-stylesheet', resource_path('css/filament/admin/custom.css')),
                Js::make('admin-custom-script', resource_path('js/filament/admin/custom.js')),
            ])
            ->simplePageMaxContentWidth(MaxWidth::Small)
            ->bootUsing(function (): void {
                // Register a render hook for the login form.
                FilamentView::registerRenderHook(
                    'panels::auth.login.form.after',
                    fn(): \Illuminate\View\View => view('filament.login_extra'),
                );
            })
            //  ->authGuard('admins')
            ->passwordReset()
            ->emailVerification()
            ->tenantBillingRouteSlug('billing')
            ->loginRouteSlug('admin-login')             // e.g. /admin/admin-login
            //->homeRoute('filament.admin.dashboard')
            ->registrationRouteSlug('join-us')                       // e.g. /admin/join-us
            ->passwordResetRoutePrefix('account/password')           // e.g. /admin/account/password/request & /admin/account/password/reset
            ->passwordResetRequestRouteSlug('request')
            ->passwordResetRouteSlug('reset')
            ->emailVerificationRoutePrefix('account/verify-email')   // e.g. /admin/account/verify-email/prompt & /admin/account/verify-email/verify
            ->emailVerificationPromptRouteSlug('prompt')
            ->emailVerificationRouteSlug('verify')
            ->registration()
            // ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class)
            //     ->profile(isSimple: false)

            //->profile()

            //->breadcrumbs(false)
            //->topbar(false)
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('1rem')
            ->userMenuItems([
                MenuItem::make()
                    ->label('Settings')
                    ->url(fn(): string => Settings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
                'profile' => MenuItem::make()->label('Edit profile'),
                'logout' => MenuItem::make()->label('Log out '),
                MenuItem::make()
                    ->label('Clear Cache')
                    ->icon('heroicon-o-trash')
                    ->postAction(fn(): string => route('filament.clear-cache'))










            ])
            //  ->navigation(false)

            /* ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make('Website')
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-home')
                                ->url(fn(): string => Dashboard::getUrl())
                                ->isActiveWhen(fn(): bool => request()->routeIs('filament.admin.pages.dashboard')),
                            ...PostResource::getNavigationItems(),

                        ]),

                ]);
            })
 */
            // ->topNavigation()
            //   ->sidebarWidth('15rem')
            ->assets([
                Css::make('filament-styles', resource_path('css/filament.css')),
            ])
            /*     ->navigationGroups([
                NavigationGroup::make('group_others')
                    ->label('Group others')
                    ->extraTopbarAttributes(['class' => 'custom-topbar'])
                    ->icon('heroicon-o-pencil'),
                NavigationGroup::make()
                    ->label('Setting Group')
                    ->icon('heroicon-o-shopping-cart')
                    ->extraTopbarAttributes(['style' => 'background-color:rgb(1, 14, 34); border: 2px solidrgb(236, 10, 48); border-radius: 4px; padding: 4px;'])
                    ->collapsible(false),

            ])
            ->navigationItems([
                NavigationItem::make('Analytics')
                    ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-presentation-chart-line')
                    ->group('Reports')
                    ->sort(3),
                NavigationItem::make('dashboard')
                    ->label('dashboard title')
                    ->url(fn(): string => Dashboard::getUrl())
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard'))
            ])
         */->colors([
                'primary' => Color::Amber,
            ])
            ->plugin(Toolkit::make()->emailVerifiedAt(true))
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')

            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,

            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                EnsureUserIsActive::class,

            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsAdmin::class,
            ]);
    }
}
