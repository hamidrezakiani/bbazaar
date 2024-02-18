<?php

namespace App\Filament\Seller\Widgets;

use App\Models\Category;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CategoryChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Top Categories';
    protected static ?int $sort = 8;
    protected int|string|array $columnSpan = 1;
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

        $topCategory = Category::query()
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')
            ->when($activeFilter, function ($query) use ($activeFilter) {
                return $query->whereMonth('ordered_products.created_at', $activeFilter);
            })
            ->select(
                'categories.id',
                'categories.title',
                DB::raw("categories.title as total"),
                DB::raw("(SUM(ordered_products.selling)) as total_price")
            )->whereMonth('ordered_products.created_at', $currentMonth)
            ->where('products.admin_id', auth()->user()->id)
            ->orderBy('total_price', 'DESC')
            ->groupBy('categories.id')
            ->get();

        $totalPrice = $topCategory->pluck('total_price')->toArray();
        $total = $topCategory->pluck('total')->toArray();



        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales',
                    'data' => $totalPrice,
                    'backgroundColor' => [
                        '#2dd4bf', '#003159', '#feb019', '#ff455f', '#775dd0', '#80effe',
                    ],
                    'cutout' => '55%',
                    'hoverOffset' => 10,
                    'borderColor' => 'transparent',
                ],
            ],

            'labels' => $total,
        ];
    }

    protected function getOptions(): array | RawJs | null
    {
        return RawJs::make(<<<'JS'
            {
                animation: {
                    duration: 0,
                },
                elements: {
                    point: {
                        radius: 0,
                    },
                    hit: {
                        radius: 0,
                    },

                },
                maintainAspectRatio: false,
                borderRadius: 4,
                scaleBeginAtZero: true,
                radius: '85%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'left',
                        align: 'bottom',
                        labels: {
                            usePointStyle: true,
                            font: {
                                size: 10
                            }
                        }
                    },
                },
                scales: {
                    x: {
                        display: false,
                    },
                    y: {
                        display: false,
                    },
                },
                tooltips: {
                    enabled: true,
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

{

}
