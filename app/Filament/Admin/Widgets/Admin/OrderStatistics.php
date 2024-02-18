<?php

namespace App\Filament\Admin\Widgets\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Helper\Setting;
use App\Models\Product;
use Filament\Forms\Form;
use App\Models\OrderedProduct;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Forms\Concerns\InteractsWithForms;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use AymanAlhattami\FilamentDateScopesFilter\DateScopeFilter;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;

class OrderStatistics extends BaseWidget implements HasForms
{
    use InteractsWithForms;
    use HasWidgetShield;

    public $created_at;
    public $date_range;

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    public $orders;
    protected static ?string $pollingInterval = '120s';

    public ?string $filter = 'today';

    protected function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    // Flatpickr::make('date_range')
                    //     ->range()
                    //     ->placeholder('Select a date range')
                    //     ->hiddenLabel(),
                    Select::make('created_at')
                        ->hiddenLabel()
                        ->reactive()
                        ->options([
                            'today' => 'Today',
                            'week' => 'Last week',
                            'month' => 'Last month',
                            'year' => 'This year',
                        ])
                        ->prefixIcon('tabler-calendar'),
                ])->columns(1),
            ]);
        // $activeFilter = $this->filter;
    }


    protected function getStats(): array
    {
        $date = $this->created_at;
        $dateRange = $this->date_range;

        $cancelled = Order::where('cancelled', 1)->count();
        //        $orders = Order::where('cancelled', '!=', 1);
        //        $orders->when(
        //            $date,
        //            function ($orders, $date) {
        //                if ($date == 'today') {
        //                    return $orders->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
        //                } else if ($date == 'last_week') {
        //                    return $orders->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        //                } else if ($date == 'last_month') {
        //                    return $orders->whereBetween('created_at', [Carbon::now()->subMonth(1)->startOfMonth(), Carbon::now()->subMonth(1)->endOfMonth()]);
        //                } else if ($date == 'last_year') {
        //                    return $orders->whereBetween('created_at', [Carbon::now()->subYear(1)->startOfYear(), Carbon::now()->subYear(1)->endOfYear()]);
        //                }
        //            }
        //        );
        //
        //        $orders->select("status", DB::raw("(count(id)) as total"))
        //            ->groupBy('status')
        //            ->get();

        $statistics = Order::query()
            ->when(
                $date,
                function ($statistics, $date) {
                    if ($date == 'today') {
                        return $statistics->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
                    } else if ($date == 'week') {
                        return $statistics->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    } else if ($date == 'month') {
                        return $statistics->whereBetween('created_at', [Carbon::now()->subMonth(1)->startOfMonth(), Carbon::now()->subMonth(1)->endOfMonth()]);
                    } else if ($date == 'year') {
                        return $statistics->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                    }
                }
            )
            ->select("status")
            ->groupBy('status')
            ->get();

        $totalPending = 0;
        $totalConfirmed = 0;
        $totalPickUp = 0;
        $totalOnWay = 0;
        $totalDelivered = 0;

        foreach ($statistics as $status) {
            if ($status->status == 1) $totalPending = $status->where('cancelled',0)->where('status', 1)->count();
            elseif ($status->status == 2) $totalConfirmed = $status->where('cancelled',0)->where('status', 2)->count();
            elseif ($status->status == 3) $totalPickUp = $status->where('cancelled',0)->where('status', 3)->count();
            elseif ($status->status == 4) $totalOnWay = $status->where('cancelled',0)->where('status', 4)->count();
            elseif ($status->status == 5) $totalDelivered = $status->where('cancelled',0)->where('status', 5)->count();
        }

        return [
            Stat::make('Products', '')
                ->view('filament.admin.widgets.dashboard.orderStatistics', [
                    'cancelled' => $cancelled,
                    'totalPending' => $totalPending,
                    'totalConfirmed' => $totalConfirmed,
                    'totalPickUp' => $totalPickUp,
                    'totalOnWay' => $totalOnWay,
                    'totalDelivered' => $totalDelivered,
                ]),
        ];
    }
}
