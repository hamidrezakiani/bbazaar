<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VoucherResource\RelationManagers\LanguageRelationManager;
use App\Models\Order;
use App\Models\VoucherLang;
use Exception;
use Filament\Forms;
use Filament\Tables;
use App\Models\Setting;
use App\Models\Voucher;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\Helper\FileHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Grid;
use App\Models\BannerSourceSubCategory;
use Filament\Notifications\Notification;
use App\Models\HomeSliderSourceSubCategory;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Wallo\FilamentSelectify\Components\ToggleButton;
use App\Filament\Admin\Resources\Admin\VoucherResource\Pages;
use App\Filament\Admin\Resources\Admin\VoucherResource\RelationManagers;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;
    protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationIcon = 'tabler-ticket';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Voucher Form')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('price')
                                ->prefix(fn() => Setting::first()?->currency_icon)
                                ->numeric()
                                ->required(),
                            Forms\Components\Select::make('type')
                                ->options([
                                    1 => 'Flat',
                                    2 => 'Percent'
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('capped_price')
                                ->prefix(fn() => Setting::first()?->currency_icon)
                                ->numeric()
                                ->label(fn() => 'Capped Price'),
                            Forms\Components\TextInput::make('min_spend')
                                ->prefix(fn() => Setting::first()?->currency_icon)
                                ->numeric()
                                ->label(fn() => 'Mini Spent')
                                ->required(),
                            Forms\Components\TextInput::make('usage_limit')
                                ->label('Usage Limit')
                                ->prefix('Order')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('limit_per_customer')
                                ->label('Limit per Customer')
                                ->prefix('Order')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('code')
                                ->prefixIcon('tabler-hash')
                                ->required(),
                            Forms\Components\DateTimePicker::make('start_time')
                                ->prefixIcon('tabler-calendar')
                                ->required(),
                            Forms\Components\DateTimePicker::make('end_time')
                                ->prefixIcon('tabler-calendar')
                                ->required(),
                            Forms\Components\Toggle::make('status')
                                ->inline(false)
                                ->onIcon('tabler-eye')
                                ->offIcon('tabler-eye-off'),
                        ])
                    ]),
                Forms\Components\Hidden::make('admin_id')
                    ->dehydrateStateUsing(fn() => Auth::user()->id)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Voucher Code'),
                Tables\Columns\TextColumn::make('price')
                    ->label(fn() => 'Price (' . Setting::first()?->currency_icon . ')')
                    ->formatStateUsing(function (Voucher $record, $state) {
                        if ($record->type == 1) {
                            return $state . ' ' . Setting::first()?->currency_icon;
                        } else {
                            return $state . '%';
                        }
                    }),
                Tables\Columns\TextColumn::make('capped_price')
                    ->label(fn() => 'Capped Price (' . Setting::first()?->currency_icon . ')'),
                Tables\Columns\TextColumn::make('min_spend')
                    ->label(fn() => 'Min Spend (' . Setting::first()?->currency_icon . ')'),
                Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Usage Limit (Order)'),
                Tables\Columns\TextColumn::make('limit_per_customer')
                    ->label('Limit per Customer (Order)'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status')
                    ->inline(false)
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),

            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {
                        DB::transaction(function () use ($record) {
                            $order = Order::where('voucher_id', $record->id)->first();
                            if ($order) {
                                throw new Exception('This voucher is currently being used by a product.');
                            }
                            VoucherLang::where('voucher_id', $record->id)->delete();
                            $record->delete();
                            Notification::make()
                                ->title('Success')
                                ->body('Voucher Successfully Deleted.')
                                ->success()
                                ->send();
                        });
                    } catch (Exception $ex) {
                        Notification::make()
                            ->title('Error')
                            ->body($ex->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                })
            ])
            ->paginationPageOptions(\App\Helper\Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            LanguageRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\VoucherResource\Pages\ListVouchers::route('/'),
            'create' => \App\Filament\Admin\Resources\VoucherResource\Pages\CreateVoucher::route('/create'),
            'edit' => \App\Filament\Admin\Resources\VoucherResource\Pages\EditVoucher::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
