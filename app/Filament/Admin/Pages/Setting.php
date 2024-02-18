<?php

namespace App\Filament\Admin\Pages;

use App\Models\Language;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Pages\Page;

class Setting extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'tabler-settings';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.admin.pages.setting';


    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Label')
                ->tabs([
                    Tabs\Tab::make('Currency')
                        ->icon('tabler-currency-dollar')
                        ->schema([
                            View::make('filament.admin.pages.setting.currency'),
                        ]),
                    Tabs\Tab::make('Address')
                        ->icon('tabler-map-search')
                        ->schema([
                            View::make('filament.admin.pages.setting.address'),
                        ]),
                    Tabs\Tab::make('Language')
                        ->badge(fn () => Language::count())
                        ->icon('tabler-language')
                        ->schema([
                            View::make('filament.admin.pages.setting.language'),
                        ]),
                    Tabs\Tab::make('Social Login')
                        ->icon('tabler-brand-facebook')
                        ->schema([
                            View::make('filament.admin.pages.setting.social-login'),
                        ]),
                    Tabs\Tab::make('SMTP Setting')
                        ->icon('tabler-mail-cog')
                        ->schema([
                            View::make('filament.admin.pages.setting.smtp-setting'),
                        ]),
                    Tabs\Tab::make('Analytics')
                        ->icon('tabler-presentation-analytics')
                        ->schema([
                            View::make('filament.admin.pages.setting.analytics'),
                        ]),
                    Tabs\Tab::make('Payment')
                        ->icon('tabler-brand-paypal')
                        ->schema([
                            View::make('filament.admin.pages.setting.payment'),
                        ]),
                    Tabs\Tab::make('Media Storage')
                        ->icon('tabler-server-cog')
                        ->schema([
                            View::make('filament.admin.pages.setting.media-storage'),
                        ]),
                    Tabs\Tab::make('Miscellaneous')
                        ->icon('tabler-server-cog')
                        ->schema([
                            View::make('filament.admin.pages.setting.miscellaneous'),
                        ]),
                    Tabs\Tab::make('Clear Cache')
                        ->icon('tabler-eraser')
                        ->schema([
                            View::make('filament.admin.pages.setting.clear-cache'),
                        ]),
                ])
        ]);
    }

    public function mount()
    {
    }
}
