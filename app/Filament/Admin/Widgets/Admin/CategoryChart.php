<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Category;
use Carbon\Carbon;
use App\Models\OrderedProduct;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use App\Models\Currency;
use NumberFormatter;

class CategoryChart extends ChartWidget
{
    use HasWidgetShield;
    
    protected static ?string $heading = 'Top Categories';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '120s';

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
        
        $defaultCurrency = 'AFN';
        // Assuming 1 USD = 0.1 AFN, adjust the conversion rate as per your needs
        $conversionRate = Currency::where('code','USD')->first()?->price;

        $formatter = new NumberFormatter('en_US', NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        $topCategory = Category::query()->join('products', 'products.category_id', '=', 'categories.id')
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', 'ordered_products.order_id')
            ->where('orders.cancelled', '!=', 1)
            ->when($activeFilter, function ($query) use ($activeFilter) {
                return $query->whereMonth('ordered_products.created_at', $activeFilter);
            })
            ->select(
                'categories.id',
                'categories.title',
                DB::raw("categories.title as total"),
                DB::raw('SUM(CASE
                    WHEN orders.currency = "'.$defaultCurrency.'" THEN ordered_products.selling * ordered_products.quantity
                    ELSE ordered_products.selling * ordered_products.quantity * "'.$conversionRate.'"
                END) as total_price'),
            )->whereMonth('ordered_products.created_at', $currentMonth)
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
                    'hoverOffset' => 5,
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
