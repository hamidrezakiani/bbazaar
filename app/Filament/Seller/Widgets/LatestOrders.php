<?php

namespace App\Filament\Seller\Widgets;

use App\Enums\Status;
use App\Filament\Admin\Resources\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\HtmlString;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Order::query()
                    ->join('ordered_products as op', function ($join) {
                        $join->on('op.order_id', '=', 'orders.id');
                        $join->join('products as p', function ($join2) {
                            $join2->on('p.id', '=', 'op.product_id');
                            $join2->where('p.admin_id', auth()->user()->id);
                        });
                    })
                    ->select('orders.*')
                    ->where('orders.cancelled', '!=',Status::PUBLIC);
            })
            ->columns([
                Tables\Columns\TextColumn::make('order')->searchable(),
                Tables\Columns\TextColumn::make('id')
                    ->badge()
                    ->label('OrderStatus')
                    ->color(fn($record) => match ($record->status) {
                        1 => 'warning',
                        2, 3, 4 => 'gray',
                        5 => 'success',
                        6 => 'danger',
                    })->formatStateUsing(fn($record) => match ($record->status) {
                        1 => 'Pending',
                        2 => 'Confirmed',
                        3 => 'Picked up',
                        4 => 'On the Way',
                        5 => 'Delivered',
                        6 => 'Cancelled',
                    }),

                Tables\Columns\TextColumn::make('order_method')->formatStateUsing(fn($state) => match ($state) {
                    1 => 'Razorpay',
                    2 => 'Cash on delivery',
                    3 => 'Stripe',
                    4 => 'Paypal',
                    5 => 'Flutterwave',
                    6 => 'Iyzico Payment',
                    7 => 'Bank Transfer'
                })
                    ->label('Payment Method'),
                Tables\Columns\TextColumn::make('payment_done')->formatStateUsing(fn($state) => match ($state) {
                    1 => 'Paid',
                    0 => 'Unpaid',
                })->label('Payment OrderStatus'),
                Tables\Columns\TextColumn::make('cancelled')
                    ->badge()
                    ->label('Cancelled')
                    // ->visible(fn ($state) => $state === 1)
                    ->color('danger')
                    ->formatStateUsing(fn($state) => $state === 1 ? 'Yes' : ''),
                Tables\Columns\TextColumn::make('voucher.title')
                    ->formatStateUsing(fn($state) => dd($state)),
                Tables\Columns\TextColumn::make('address.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(fn() => 'Amount (' . Setting::first()?->currency_icon . ')')
                    ->formatStateUsing(fn($state) => number_format($state))
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->label('Total Amount')
                            ->formatStateUsing(fn($state) => new HtmlString('<b>' . number_format($state) . Setting::first()?->currency_icon . '</b>')),
                    ]),
                Tables\Columns\TextColumn::make('created_at')->formatStateUsing(fn($state) => $state->format('F j, Y - g:i A')),
                Tables\Columns\SelectColumn::make('status')->label('Order OrderStatus')
                    ->options([
                        1 => 'PENDING',
                        2 => 'CONFIRMED',
                        3 => 'PICKED_UP',
                        4 => 'ON_THE_WAY',
                        5 => 'DELIVERED'
                    ])->disabled(fn($state, $record) => $state === 5 || $record->cancelled === 1)
            ])->poll('10s');
    }
}
