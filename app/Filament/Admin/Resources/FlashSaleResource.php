<?php

namespace App\Filament\Admin\Resources;

use App\Models\FlashSaleLang;
use App\Models\FlashSaleProduct;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables;
use App\Helper\Select;
use App\Models\Product;
use App\Models\Setting;
use Filament\Forms\Form;
use App\Models\FlashSale;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Wallo\FilamentSelectify\Components\ToggleButton;
use App\Filament\Admin\Resources\FlashSaleResource\Pages;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use App\Filament\Admin\Resources\FlashSaleResource\RelationManagers;
use Filament\Forms\Components\Actions\Action;

class FlashSaleResource extends Resource
{
    protected static ?string $model = FlashSale::class;
    protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationIcon = 'tabler-bolt';
    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Flash Sale Form')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required(),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->prefixIcon('tabler-calendar')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->prefixIcon('tabler-calendar')
                            ->required()
                    ]),
                    Toggle::make('status')
                        ->inline(false)
                        ->onIcon('tabler-eye')
                        ->offIcon('tabler-eye-off'),
                    Hidden::make('admin_id')
                        ->dehydrateStateUsing(fn() => Auth::user()->id)
                ]),

                Forms\Components\Section::make('Flash Products')
                    ->headerActions([
                        Action::make('reset')
                            ->modalHeading('Are you sure?')
                            ->modalDescription('All existing items will be removed from the order.')
                            ->requiresConfirmation()
                            ->color('danger')
                            ->action(fn (Forms\Set $set) => $set('items', [])),
                    ])
                    ->schema([
                        static::getItemsRepeater(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('products')
                    ->formatStateUsing(fn(FlashSale $record) => $record->products->count()),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('OrderStatus')
                    ->inline(false)
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),
//                Tables\Columns\TextColumn::make('status')
//                    ->badge()
//                    ->color(fn (string $state): string => match ($state) {
//                        '2' => 'warning',
//                        '1' => 'success',
//                    })->formatStateUsing(fn ($state): string => $state === 1 ? 'Public' : 'Private'),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {
                        $flashSale = FlashSale::with('products')->find($record->id);
                        if (count($flashSale['products']) > 0) {
                            FlashSaleProduct::where(['flash_sale_id' => $record->id])->delete();
                        }
                        FlashSaleLang::where('flash_sale_id', $record->id)->delete();
                        if ($flashSale->delete()) {
                            Notification::make()
                                ->title('Success')
                                ->body('Flash Sale Deleted Successfully')
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
            ])
            ->paginationPageOptions(\App\Helper\Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FlashSaleLanguageRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\FlashSaleResource\Pages\ListFlashSales::route('/'),
            'create' => \App\Filament\Admin\Resources\FlashSaleResource\Pages\CreateFlashSale::route('/create'),
            'edit' => \App\Filament\Admin\Resources\FlashSaleResource\Pages\EditFlashSale::route('/{record}/edit'),
        ];
    }

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('products')
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->searchable()
                    ->label('Product')
                    ->allowHtml()
                    ->columnSpanFull()
                    ->required()
                    ->getSearchResultsUsing(function (string $search) {
                        $colors = Product::where('title', 'like', "%{$search}%")->limit(50)->get();
                        return $colors->mapWithKeys(function ($color) {
                            return [$color->getKey() => Select::getCleanProductOption($color)];
                        })->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): string {
                        $color = Product::find($value);
                        return Select::getCleanProductOption($color);
                    })
                    ->reactive()
                    ->afterStateHydrated(function (Forms\Set $set, $state) {
                        $set('selling', Product::find($state)?->selling ?? 0);
                        $set('offered', Product::find($state)?->offered ?? 0);
                    })
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->afterStateUpdated(function (Forms\Set $set, $component, $state,$livewire) {
                        $set('selling', Product::find($state)?->selling ?? 0);
                        $set('offered', Product::find($state)?->offered ?? 0);
                    })->extraAttributes(['class' => 'mb-2']),
                Forms\Components\TextInput::make('selling')
                    ->prefix(Setting::first()?->currency_icon)
                    ->readOnly()
                    ->reactive(),
                Forms\Components\TextInput::make('offered')
                    ->prefix(Setting::first()?->currency_icon)
                    ->readOnly(),
                Forms\Components\TextInput::make('price')
                    ->prefix(Setting::first()?->currency_icon)
                    ->required(),
                Hidden::make('admin_id')
                    ->dehydrateStateUsing(fn() => Auth::user()->id)
            ])
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip('Open product')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(function (array $arguments, Repeater $component): ?string {
                        $itemData = $component->getRawItemState($arguments['item']);

                        $product = Product::find($itemData['product_id']);

                        if (! $product) {
                            return null;
                        }

                        return ProductResource::getUrl('edit', ['record' => $product]);
                    }, shouldOpenInNewTab: true)
                    ->hidden(fn (array $arguments, Repeater $component): bool => blank($component->getRawItemState($arguments['item'])['product_id'])),
            ])
            ->relationship('products')
            ->itemLabel(fn(array $state): ?string => Product::find($state['product_id'])?->title ?? null)
            ->columns(3)
            ->columnSpanFull()
            ->addActionLabel('Add Product')
            ->reorderable(true)
            ->hiddenLabel()
            ->grid(2);
    }
}
