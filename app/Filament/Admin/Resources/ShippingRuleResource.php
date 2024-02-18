<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Product;
use Filament\Forms\Form;
use Squire\Models\Region;
use Squire\Models\Country;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\ShippingRule;
use App\Models\ShippingPlace;
use App\Models\ShippingRuleLang;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use App\Filament\Admin\Resources\Admin\ShippingRuleResource\Pages;
use App\Filament\Admin\Resources\Admin\ShippingRuleResource\RelationManagers;
use App\Filament\Admin\Resources\ShippingRuleResource\RelationManagers\LanguageRelationManager;

class ShippingRuleResource extends Resource
{
    protected static ?string $model = ShippingRule::class;
    protected static ?string $navigationIcon = 'tabler-truck-delivery';
    protected static ?string $navigationGroup = 'Product';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Shipping Rule Form')
                    ->schema([
                        Forms\Components\TextInput::make('title'),
                        Forms\Components\Toggle::make('single_price')
                            ->label('Single shipping price for an order'),
//                        FormInput\Components\Section::make('Value')->schema([
                        Forms\Components\Repeater::make('Places')
                            ->relationship('shipping_places')
                            ->hiddenLabel()
                            ->schema([
                                Forms\Components\Grid::make()->schema([
//                                    Forms\Components\Select::make('country')
//                                        ->options(Country::all()->pluck('name', 'id'))
//                                        ->searchable()
//                                        ->prefixIcon('tabler-world')
//                                        ->live()
//                                        ->required(),
                                    Forms\Components\Select::make('country')
                                        ->options(function () {
                                           return (new \App\Models\ShippingRule)->getCountries();
                                        })
                                        ->searchable()
                                        ->prefixIcon('tabler-world')
                                        ->live()
                                        ->required(),

                                    Forms\Components\Select::make('state')
                                        ->prefixIcon('tabler-building-castle')
                                        ->searchable()
                                        ->required()
                                        ->options(function (Forms\Get $get) {
                                            $stateData = (new \App\Models\ShippingRule)->getStates($get('country'));
                                            $collection = collect($stateData);
                                            return $collection->pluck('name', 'code');
                                        }),
                                    Forms\Components\TextInput::make('price')
                                        ->numeric()
                                        ->prefixIcon('tabler-coin')
                                        ->required(),
                                    Forms\Components\TextInput::make('day_needed')
                                        ->prefixIcon('tabler-calendar')
                                        ->numeric()
                                        ->required(),
                                    Forms\Components\Toggle::make('pickup_point')
                                        ->live(onBlur: true),
                                ])->columns(2),
                                Forms\Components\Grid::make()->schema([
                                    Forms\Components\TextInput::make('pickup_price')
                                        ->prefix('Price($)')
                                        ->numeric()
                                        ->hiddenLabel()
                                        ->required(),
//                                    FormInput\Components\TextInput::make('pickup_phone')
//                                        ->prefix('Phone')
//                                        ->hiddenLabel()
//                                        ->required(),
                                    PhoneInput::make('pickup_phone')
                                        ->required()
                                        ->hiddenLabel(),
//                                        ->mask(RawJs::make(<<<'JS'
//                                                $input.startsWith('34') || $input.startsWith('37') ? '9999 999999 99999' : '9999 9999 9999 9999'
//                                        JS)),
                                    Forms\Components\TextInput::make('pickup_address_line_1')
                                        ->prefix('Address 1')
                                        ->columnSpanFull()
                                        ->hiddenLabel()
                                        ->required(),
                                    Forms\Components\TextInput::make('pickup_address_line_2')
                                        ->prefix('Address 2')
                                        ->columnSpanFull()
                                        ->hiddenLabel()
                                        ->required(),
                                    Forms\Components\Grid::make()->schema([
                                        Forms\Components\TextInput::make('pickup_city')
                                            ->prefix('City')
                                            ->hiddenLabel()
                                            ->required(),
                                        Forms\Components\TextInput::make('pickup_state')
                                            ->prefix('State')
                                            ->hiddenLabel()
                                            ->required(),
                                        Forms\Components\TextInput::make('pickup_zip')
                                            ->prefix('Zip')
                                            ->hiddenLabel()
                                            ->required(),
                                        Forms\Components\TextInput::make('pickup_country')
                                            ->prefix('Country')
                                            ->hiddenLabel()
                                            ->required(),
                                    ])->columns(2)
                                ])
                                    ->visible(fn(Get $get) => $get('pickup_point')),
                                Forms\Components\Hidden::make('admin_id')
                                    ->dehydrateStateUsing(fn() => Auth::user()->id)
                            ])->columns(4)
                            ->itemLabel(fn(array $state): ?string => Country::find($state['country'])?->name ?? null)
                            ->cloneable()
                            ->addActionLabel('Add Shipping Place')
                            ->grid(2),
//                        ]),
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
                Tables\Columns\TextColumn::make('shipping_places.country')
                ->label('Shipping Place')
                ->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {
                        $product = Product::where('shipping_rule_id', $record->id)->first();
                        if ($product) {
                            Notification::make()
                                ->title('Error')
                                ->body('Shipping Rule use by product.')
                                ->danger()
                                ->send();
                        } else {
                            ShippingPlace::where('shipping_rule_id', $record->id)->delete();
                            ShippingRuleLang::where('shipping_rule_id', $record->id)->delete();
                            $record->delete();
                            Notification::make()
                                ->title('Deleted')
                                ->body('Shipping Rule Successfully Deleted.')
                                ->success()
                                ->send();
                        }
                    } catch (\Exception $ex) {
                        Notification::make()
                            ->title('Error')
                            ->body($ex->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LanguageRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\ShippingRuleResource\Pages\ListShippingRules::route('/'),
            'create' => \App\Filament\Admin\Resources\ShippingRuleResource\Pages\CreateShippingRule::route('/create'),
            'edit' => \App\Filament\Admin\Resources\ShippingRuleResource\Pages\EditShippingRule::route('/{record}/edit'),
        ];
    }
}
