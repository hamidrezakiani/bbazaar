<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class OrderStatusChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'orderStatusChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Order Status';

    /**
     * Sort
     */
    protected static ?int $sort = 4;

    /**
     * Widget content height
     */
    protected static ?string $maxHeight = '315px';
    protected static ?string $pollingInterval = '120s';

    /**
     * Widget Footer
     */
    protected function getFooter(): string|View
    {
        $data = [
            'pending' => Order::where([['status', 1], ['cancelled', 0]])->count(),
            'delivered' => Order::where('status', 5)->count(),
            'cancelled' => Order::where('cancelled', 1)->count(),
        ];

        return view('filament.admin.widgets.dashboard.order-status-footer', ['data' => $data]);
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {

        $orders = Order::selectRaw('COUNT(*) as count, status')
            ->whereIn('status', [1, 5])
            ->groupBy('status')
            ->get();

        $chartData = $orders->pluck('count');

        $totalOrders = $orders->sum('count');
        $deliveredOrders = $orders->where('status', 5)->sum('count');

        $deliveredPercentage = 0;

        if ($totalOrders > 0) {
            $deliveredPercentage = number_format(($deliveredOrders / $totalOrders) * 100,2);
        }



        return [
            'chart' => [
                'type' => 'radialBar',
                'height' => 325,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [$deliveredPercentage],
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => -140,
                    'endAngle' => 130,
                    'hollow' => [
                        'size' => '60%',
                        'background' => 'transparent',
                    ],
                    'track' => [
                        'background' => 'transparent',
                        'strokeWidth' => '100%',
                    ],
                    'dataLabels' => [
                        'show' => true,
                        'name' => [
                            'show' => true,
                            'offsetY' => -10,
                            'fontWeight' => 600,
                            'fontFamily' => 'inherit',
                        ],
                        'value' => [
                            'show' => true,
                            'fontWeight' => 600,
                            'fontSize' => '24px',
                            'fontFamily' => 'inherit',
                        ],
                    ],

                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'horizontal',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#f59e0b'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 0.6,
                    'stops' => [30, 70, 100],
                ],
            ],
            'stroke' => [
                'dashArray' => 10,
            ],
            'labels' => ['Delivered'],
            'colors' => ['#16a34a'],

        ];
    }
}
