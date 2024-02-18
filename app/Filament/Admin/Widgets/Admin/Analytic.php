<?php

namespace App\Filament\Admin\Widgets\Admin;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class Analytic extends BaseWidget
{
    use HasWidgetShield;
    
    protected function getStats(): array
    {
        return [
            Stat::make('Test','')
                ->view('filament.admin.widgets.dashboard.analytic')
        ];
    }
}
