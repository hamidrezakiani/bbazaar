<?php

namespace App\Filament\Seller\Widgets;

use App\Enums\Status;
use App\Models\Order;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class OrderStatistics extends BaseWidget implements HasForms
{
    use InteractsWithForms;
    use HasWidgetShield;

    public $created_at;
    public $date_range;

    protected static ?int $sort = 3;
    public $orders;
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'today';

    protected function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
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
    }


    protected function getStats(): array
    {
        $date = $this->created_at;
        $dateRange = $this->date_range;

        $cancelled = Order::join('ordered_products as op', function ($join) {
            $join->on('op.order_id', '=', 'orders.id');
            $join->join('products as p', function ($join2) {
                $join2->on('p.id', '=', 'op.product_id');
                $join2->where('p.admin_id', auth()->user()->id);
            });
        })->where('orders.cancelled', Status::PUBLIC)
            ->when( $date,
                function ($cancelled, $date) {
                    if ($date == 'today') $cancelled->whereBetween('orders.created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
                    else if ($date == 'last_week') $cancelled->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    else if ($date == 'last_month') $cancelled->whereBetween('orders.created_at', [Carbon::now()->subMonth(1)->startOfMonth(), Carbon::now()->subMonth(1)->endOfMonth()]);
                    else if ($date == 'last_year') $cancelled->whereBetween('orders.created_at', [Carbon::now()->subYear(1)->startOfYear(), Carbon::now()->subYear(1)->endOfYear()]);
                }
            )
            ->count();

        $statistics = Order::query()->join('ordered_products as op', function ($join) {
            $join->on('op.order_id', '=', 'orders.id');
            $join->join('products as p', function ($join2) {
                $join2->on('p.id', '=', 'op.product_id');
                $join2->where('p.admin_id', auth()->user()->id);
            });
        })->where('orders.cancelled', '!=', Status::PUBLIC)
            ->select(
                "orders.id",
                "orders.created_at",
                "orders.status",
            )
            ->when( $date,
                function ($statistics, $date) {
                    if ($date == 'today') $statistics->whereBetween('orders.created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
                    else if ($date == 'week') $statistics->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    else if ($date == 'month') $statistics->whereBetween('orders.created_at', [Carbon::now()->subMonth(1)->startOfMonth(), Carbon::now()->subMonth(1)->endOfMonth()]);
                    else if ($date == 'year') $statistics->whereBetween('orders.created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
                }
            )
            ->orderBy('orders.status')
            ->groupBy('orders.status')
            ->get();

        $totalPending = 0;
        $totalConfirmed = 0;
        $totalPickUp = 0;
        $totalOnWay = 0;
        $totalDelivered = 0;

        foreach ($statistics as $status) {
            if ($status->status == 1) $totalPending = $status->where('status', 1)->count();
            elseif ($status->status == 2) $totalConfirmed = $status->where('status', 2)->count();
            elseif ($status->status == 3) $totalPickUp = $status->where('status', 3)->count();
            elseif ($status->status == 4) $totalOnWay = $status->where('status', 4)->count();
            elseif ($status->status == 5) $totalDelivered = $status->where('status', 5)->count();
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
