<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest())
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
                    ->badge()
                    ->label('Payment Method'),
                Tables\Columns\TextColumn::make('payment_done')->formatStateUsing(fn($state) => match ($state) {
                    1 => 'Paid',
                    0 => 'Unpaid',
                })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                    })
                    ->badge()
                    ->label('Payment OrderStatus'),
                Tables\Columns\TextColumn::make('cancelled')
                    ->badge()
                    ->label('Cancelled')
                    // ->visible(fn ($state) => $state === 1)
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'danger',
                    })
                    ->formatStateUsing(fn($state) => $state === 1 ? 'Yes' : '-'),
                Tables\Columns\TextColumn::make('voucher.title')
                    ->formatStateUsing(fn($state) => dd($state)),
                Tables\Columns\TextColumn::make('address.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->currency === 'AFN') return $state . '؋';
                        if ($record->currency === 'USD') return $state . '$';
                    }),
                Tables\Columns\TextColumn::make('created_at')->formatStateUsing(fn ($state) => $state->format('F j, Y - g:i A')),
                Tables\Columns\SelectColumn::make('status')->label('Status')
                    ->options([
                        1 => 'PENDING',
                        2 => 'CONFIRMED',
                        3 => 'PICKED_UP',
                        4 => 'ON_THE_WAY',
                        5 => 'DELIVERED'
                    ])
                    ->afterStateUpdated(function ($record) {
                        if($record->status == 5){
                            $record->update(['payment_done' => 1]);
                        }
                    })
                    ->disabled(fn ($state, $record) => $state === 5 || $record->cancelled === 1)
            ])->poll('120s');
    }
}
