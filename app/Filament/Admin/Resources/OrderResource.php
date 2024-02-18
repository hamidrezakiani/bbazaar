<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Filament\Admin\Resources\OrderResource\RelationManagers;
use App\Models\Cancellation;
use App\Models\Helper\MailHelper;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;
use Illuminate\Mail\Mailable;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'tabler-shopping-bag';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
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
                Tables\Columns\TextColumn::make('voucher.title'),
                Tables\Columns\TextColumn::make('address.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(fn() => 'Total Amount')
                    ->formatStateUsing(function ($state, $record) {
                        if($record->currency === 'AFN') return $state.' ؋';
                        if($record->currency === 'USD') return $state.' $';
                    }),
                Tables\Columns\TextColumn::make('created_at')->formatStateUsing(fn($state) => \App\Helper\Setting::dateTime($state)),
                Tables\Columns\SelectColumn::make('status')->label('Order OrderStatus')
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\OrderResource\Pages\ListOrders::route('/'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
//                Components\Section::make()
//                    ->schema([
//                        Components\Split::make([
//                            Components\Grid::make(2)
//                                ->schema([
//                                    Components\Group::make([
//                                        Components\TextEntry::make('address.name')
//                                            ->formatStateUsing(fn($state): HtmlString => new HtmlString('<b>' . $state . '</b>'))
//                                            ->hiddenLabel(),
//                                        Components\TextEntry::make('address.id')->hiddenLabel()
//                                            ->formatStateUsing(function ($state) {
//                                                $address = UserAddress::find($state);
//                                                return $address->address_1 . ', ' . $address->address_2 . ', ' . $address->city . '-' . $address->zip . ',' . Region::where('code', $address->state)->first()?->name . ',' . Country::where('code', $address->country)->first()?->name;
//                                            }),
//                                        Components\TextEntry::make('address.email')
//                                            ->formatStateUsing(fn($state): HtmlString => new HtmlString('Email: ' . $state))
//                                            ->hiddenLabel(),
//                                        Components\TextEntry::make('address.phone')
//                                            ->formatStateUsing(fn($state): HtmlString => new HtmlString('Phone: ' . $state))
//                                            ->hiddenLabel(),
//                                    ]),
//                                    Components\Group::make([
//                                        Components\TextEntry::make('order')->inlineLabel(),
//                                        Components\TextEntry::make('order_method')->formatStateUsing(fn($state) => match ($state) {
//                                            1 => 'Razorpay',
//                                            2 => 'Cash on delivery',
//                                            3 => 'Stripe',
//                                            4 => 'Paypal',
//                                            5 => 'Flutterwave',
//                                            6 => 'Iyzico Payment'
//                                        })->inlineLabel()
//                                            ->label('Payment Method'),
//                                        Components\TextEntry::make('payment_done')->formatStateUsing(fn($state) => match ($state) {
//                                            1 => 'Paid',
//                                            0 => 'Unpaid',
//                                        })->inlineLabel()
//                                            ->label('Payment OrderStatus'),
//                                        Components\TextEntry::make('created_at')->inlineLabel(),
//                                        Components\TextEntry::make('total_amount')
//                                            ->formatStateUsing(fn($state) => $state . Setting::first()?->currency_icon)
//                                            ->inlineLabel(),
//                                    ]),
//                                ]),
//                        ])->from('lg'),
//                    ]),
                Components\Group::make()->columnSpanFull()
                    ->schema([
                        Components\TextEntry::make('id')->hiddenLabel()
                            ->view('filament.resources.components.order.ordered-product')
                    ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
