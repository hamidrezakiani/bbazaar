<?php

namespace App\Filament\Seller\Widgets;


use App\Models\OrderedProduct;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthIncome extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Daily Sales';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';

    protected function getFilters(): ?array
    {
        return [
            '' => 'Select Month',
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $currentMonth = Carbon::now()->month;
        $activeFilter = $this->filter;
        $adminId = Auth::user()->id;
        $chartData = OrderedProduct::join('products as p', function ($join) use ($adminId) {
            $join->on('p.id', '=', 'ordered_products.product_id');
            $join->where('p.admin_id', $adminId);
        })->when($activeFilter, function ($query) use ($activeFilter) {
                return $query->whereMonth('ordered_products.created_at', $activeFilter);
            })->select(
            DB::raw('SUM(ordered_products.selling * ordered_products.quantity) as amount'),
                DB::raw('DAY(ordered_products.created_at) as day'),
                DB::raw('DATE_FORMAT(ordered_products.created_at, "%W") as day_name')
            )
            ->whereMonth('ordered_products.created_at', $currentMonth)
            ->orderBy('day')
            ->groupBy('day', 'day_name')
            ->get();

        $day = $chartData->pluck('day')->toArray();
        $dayName = $chartData->pluck('day_name')->toArray();
        $currency = '$';

        $value = $chartData->pluck('amount')->map(function ($amount) {
            return number_format($amount, 2);
        })->toArray();

        if ($activeFilter != '') {
            $daysOfMonth = Carbon::now()->month($activeFilter)->daysInMonth;
        } else {
            $daysOfMonth = Carbon::now()->daysInMonth;
        }

        $labels = [];
        $data = [];

        for ($dayOfMonth = 1; $dayOfMonth <= $daysOfMonth; $dayOfMonth++) {
            $index = array_search($dayOfMonth, $day);
            $labels[] = $dayOfMonth;

            if ($index !== false) {
                $data[] = number_format($value[$index], 2);
            } else {
                $data[] = null;
            }
        }


        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales',
                    'data' => $data,
                    'borderRadius' => 5,
                ],

            ],

            'labels' => $labels,
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<JS
        {
                animation: {
                    duration: 1500,
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                },


            scales: {
                y: {
                    ticks: {
                        callback: (value) => '؋' + value,
                    },
                },
            },
        }
    JS
        );
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
