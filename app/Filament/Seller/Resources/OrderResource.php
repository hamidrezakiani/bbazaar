<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\OrderResource\Pages;
use App\Filament\Seller\Resources\OrderResource\RelationManagers;
use App\Models\Cancellation;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'tabler-shopping-bag';
    protected static ?string $navigationGroup = 'Order';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\TextColumn::make('voucher.title')
                    ->formatStateUsing(fn($state) => dd($state)),
                Tables\Columns\TextColumn::make('address.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(fn() => 'Amount (' . Setting::first()?->currency_icon . ')')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->label('Total Amount')
                            ->formatStateUsing(fn($state) => new HtmlString('<b>' . number_format($state) . Setting::first()?->currency_icon . '</b>')),
                    ]),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::CustomDelete('Delete')
                    ->action(function ($record) {
                        try {
                            OrderedProduct::where('order_id', $record->id)->delete();
                            Cancellation::where('order_id', $record->id)->delete();
                            if ($record->delete()) {
                                Notification::make()
                                    ->title('Deleted')
                                    ->body('Order Successfully Deleted.')
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $ex) {
                            Notification::make()->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                \App\Helper\Actions::ViewAction()
                    ->modalHeading(fn ($record):HtmlString => new HtmlString("<b>View Invoice</b> {$record->order}"))
            ])
            ->paginationPageOptions(\App\Helper\Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Group::make()->columnSpanFull()
                    ->schema([
                        Components\TextEntry::make('id')->hiddenLabel()
                            ->view('filament.resources.components.order.ordered-product')
                    ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = 0;
        if(Auth::check()){
            $adminId = auth()->user()->id;
            $query = Order::query();
            $query = $query->whereHas('ordered_products.product', function ($query) use ($adminId) {
                $query->where('admin_id', $adminId);
            });

            $count = $query->count() ?? 0;
        }
        return $count;
    }


    public static function getModelLabel(): string
    {
        return 'Order';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Orders';
    }
}
