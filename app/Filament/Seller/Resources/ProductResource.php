<?php

namespace App\Filament\Seller\Resources;


use App\Filament\Seller\Resources\ProductResource\RelationManagers\ProductImagesRelationManager;
use App\Filament\Admin\Resources\ProductResource\RelationManagers\VariantRelationManager;
use App\Models\Cart;
use App\Models\CollectionWithProduct;
use App\Models\CompareList;
use App\Models\FlashSaleProduct;
use App\Models\Helper\FileHelper;
use App\Models\InventoryAttribute;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\ProductLang;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use App\Models\UpdatedInventory;
use App\Models\UserWishlist;
use App\Models\WysiwygImage;
use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use App\Helper\Select;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use Filament\Forms\Form;
use App\Models\Attribute;
use Filament\Tables\Table;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use App\Models\AttributeValue;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\Indicator;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Admin\Resources\Seller\ProductResource\Pages;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;
use App\Filament\Admin\Resources\Seller\ProductResource\RelationManagers;
use App\Filament\Admin\Resources\ProductResource\RelationManagers\LanguageRelationManager;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationIcon = 'tabler-package';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Product Details')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                if ($operation !== 'create') {
                                                    return;
                                                }
                                                $set('slug', Str::slug($state));
                                            })->columnSpanFull(),

                                        Forms\Components\TextInput::make('slug')
                                            ->columnSpanFull()
                                            ->dehydrated()
                                            ->required()
                                            ->unique(Product::class, 'slug', ignoreRecord: true),

                                        Forms\Components\Select::make('category_id')
                                            ->allowHtml()
                                            ->label('Category')
                                            ->searchable()
                                            ->required()
                                            ->getSearchResultsUsing(function (string $search) {
                                                $category = Category::where('title', 'like', "%{$search}%")->where('status', 1)->limit(50)->get();
                                                return $category->mapWithKeys(function ($category) {
                                                    return [$category->getKey() => Select::getCleanCategoryOption($category)];
                                                })->toArray();
                                            })
                                            ->getOptionLabelUsing(function ($value): string {
                                                $category = Category::find($value);
                                                return Select::getCleanCategoryOption($category);
                                            }),

                                        Forms\Components\Select::make('subcategory_id')
                                            //->relationship('sub_category', 'title')
                                            ->label('Sub Category')
                                            ->allowHtml()
                                            ->searchable()
                                            ->required()
                                            ->getSearchResultsUsing(function (string $search) {
                                                $subCateg = SubCategory::where('title', 'like', "%{$search}%")->where('status', 1)->limit(50)->get();
                                                return $subCateg->mapWithKeys(function ($subCate) {
                                                    return [$subCate->getKey() => Select::getCleanSubCategoryOption($subCate)];
                                                })->toArray();
                                            })
                                            ->getOptionLabelUsing(function ($value): string {
                                                $subCate = SubCategory::find($value);
                                                return Select::getCleanSubCategoryOption($subCate);
                                            }),

                                        Forms\Components\Select::make('brand_id')
                                            ->allowHtml()
                                            ->searchable()
                                            ->label('Brand')
                                            ->required()
                                            ->getSearchResultsUsing(function (string $search) {
                                                $brands = Brand::where('title', 'like', "%{$search}%")->where('status', 1)->limit(50)->get();
                                                return $brands->mapWithKeys(function ($brand) {
                                                    return [$brand->getKey() => Select::getCleanBrandOption($brand)];
                                                })->toArray();
                                            })
                                            ->getOptionLabelUsing(function ($value): string {
                                                $brand = Brand::find($value);
                                                return Select::getCleanBrandOption($brand);
                                            }),

                                        Forms\Components\Grid::make(3)->schema([
                                            Forms\Components\Select::make('tax_rule_id')
                                                ->searchable()
                                                ->relationship('tax_rules', 'title'),
                                            Forms\Components\Select::make('shipping_rule_id')
                                                ->searchable()
                                                ->relationship('shipping_rule', 'title'),
                                            Forms\Components\Select::make('bundle_deal_id')
                                                ->searchable()
                                                ->relationship('bundle_deal', 'title'),
                                        ]),
                                        Forms\Components\CheckboxList::make('collections')
                                            ->relationship('product_collections', 'title')
                                            ->columns(3)
                                            ->columnSpanFull()
                                            ->gridDirection('row')
                                    ])
                                    ->columns(2),
                            ]),
                        Forms\Components\Section::make('Overview')
                            ->collapsible()
                            ->schema([
                                Forms\Components\MarkdownEditor::make('overview')
                                    ->HiddenLabel()
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Section::make('Description')
                            ->collapsible()
                            ->collapsed()
                            ->schema([
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpan('full')
                                    ->required()
                            ]),
                        Forms\Components\Section::make('SEO')->schema([
                            Forms\Components\TextInput::make('meta_title')
                                ->required(),
                            Forms\Components\RichEditor::make('meta_description')
                                ->HiddenLabel()
                                ->columnSpanFull()
                        ]),
                    ])->columnSpan(['lg' => 4]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Pricing / Inventory')->schema([
                            Forms\Components\TextInput::make('purchased')
                                ->label(fn() => 'Purchased (' . Setting::first()?->currency_icon . ')')
                                ->required()
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                            Forms\Components\TextInput::make('selling')
                                ->label(fn() => 'Selling (' . Setting::first()?->currency_icon . ')')
                                ->required()
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                            Forms\Components\TextInput::make('offered')
                                ->label(fn() => 'Offered (' . Setting::first()?->currency_icon . ')')
                                ->required()
                                ->numeric()
                                ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/']),
                            Forms\Components\TextInput::make('unit')
                                ->label('Unit (Piece, KG)')
                                ->required(),
                            Forms\Components\TextInput::make('badge'),
                            Forms\Components\Fieldset::make('qty1')->schema([
                                Forms\Components\TextInput::make('quantity')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->required(),
                            ])->relationship('qty1')
                                ->label('Quantity')
                                ->columnSpanFull()
                        ])->columns(2),
                        Forms\Components\Section::make('Media')
                            ->schema([
                                FileUpload::make('video')
                                    ->label('Video')
                                    ->disk('public')
                                    ->getUploadedFileNameForStorageUsing(
                                        function (TemporaryUploadedFile $file): string {
                                            $image_info = FileHelper::uploadToLocalFilament($file, 'product-video', false);
                                            return $image_info['name'];
                                        })
                                    ->maxSize(2048)
                                    ->helperText('Max Size: 2MB'),

                                FileUpload::make('video_thumb')
                                    ->label('Thumb Video')
                                    ->disk('public')
                                    ->getUploadedFileNameForStorageUsing(
                                        function (TemporaryUploadedFile $file): string {
                                            $thumb_info = FileHelper::generateVideoThumbnail($file, 'product-video');
                                            return $thumb_info['name'];
                                        })
                                    ->maxSize(2048)
                                    ->helperText('Max Size: 2MB'),

                                FileUpload::make('image')
                                    ->label('Image')
                                    ->hiddenLabel()
                                    ->disk('public')
                                    ->image()
                                    ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                                    ->getUploadedFileNameForStorageUsing(
                                        function (TemporaryUploadedFile $file): string {
//                                                        $filename = (string)'product-' . time() . '.' . $file->guessExtension();
                                            $image_info = FileHelper::uploadToLocalFilament($file, 'product');
                                            return $image_info['name'];
                                        }),
                            ]),

                        Forms\Components\Section::make('OrderStatus')->schema([
                            Toggle::make('status')
                                ->label('OrderStatus')
                                ->inline(false)
                                ->onIcon('tabler-eye')
                                ->offIcon('tabler-eye-off'),

                            Toggle::make('refundable')
                                ->label('Refundable')
                                ->inline(false)
                                ->onIcon('tabler-check')
                                ->offIcon('tabler-x'),

                            Toggle::make('warranty')
                                ->label('Warranty')
                                ->inline(false)
                                ->onIcon('tabler-check')
                                ->offIcon('tabler-x'),

                        ]),
                    ])->columnSpan(['lg' => 2]),
                Forms\Components\Hidden::make('admin_id')
                    ->dehydrateStateUsing(fn() => Auth::user()->id)
            ])->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->words(5)
                    ->tooltip(fn($state) => $state)
                    ->extraHeaderAttributes(['style' => 'width:100%'])
                    ->wrap()
                    ->searchable(),
//                Tables\Columns\TextColumn::make('store.name')
//                    ->extraHeaderAttributes(['style' => 'width:100%'])
//                    ->view('filament.components.store-product')
//                ,
                Tables\Columns\TextColumn::make('category.title')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub_category.title')
                    ->toggleable()
                    ->sortable()
                    ->label('Sub Category'),
                Tables\Columns\TextColumn::make('brand.title')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_rules.title')
                    ->toggleable()
                    ->sortable()
                    ->label('Tax Rule'),
                Tables\Columns\TextColumn::make('purchased')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('offered')
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty.quantity')
                    ->badge()
                    ->formatStateUsing(fn($state) => number_format($state)),
                Tables\Columns\ToggleColumn::make('status')
                    ->toggleable()
                    ->sortable()
                    ->label('OrderStatus')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),
                Tables\Columns\ToggleColumn::make('refundable')
                    ->toggleable()
                    ->sortable()
                    ->label('Refund')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x'),
                Tables\Columns\ToggleColumn::make('warranty')
                    ->toggleable()
                    ->sortable()
                    ->label('Warranty')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x'),
//                Tables\Columns\TextColumn::make('status')
//                    ->badge()
//                    ->color(fn(string $state): string => match ($state) {
//                        '2' => 'warning',
//                        '1' => 'success',
//                    })->formatStateUsing(fn($state): string => $state === 1 ? 'Public' : 'Private'),
                // Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->searchable()
                    ->options(Brand::all()->pluck('title', 'id')),
                Filter::make('category_id')
                    ->form([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->reactive()
                            ->options(Category::all()->pluck('title', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('subcategory_id')
                            ->label('Sub Category')
                            ->options(function (Forms\Get $get) {
                                if ($get('category_id')) {
                                    return SubCategory::where('category_id', $get('category_id'))->pluck('title', 'id');
                                }
                            })
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['category_id'],
                                fn(Builder $query, $date): Builder => $query->where('category_id', $date),
                            )
                            ->when(
                                $data['subcategory_id'],
                                fn(Builder $query, $date): Builder => $query->where('subcategory_id', '=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['category_id'] ?? null) {
                            $indicators[] = Indicator::make('Category: ' . Category::find($data['category_id'])?->title)->removeField('from');
                        }

                        if ($data['subcategory_id'] ?? null) {
                            $indicators[] = Indicator::make('Sub Category: ' . SubCategory::find($data['subcategory_id'])?->title)->removeField('from');
                        }
                        return $indicators;
                    }),

                Filter::make('refundable')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('refundable', true)),
                Filter::make('warranty')
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->where('warranty', true))
            ])
            ->persistFiltersInSession()
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::ViewAction()
                    ->modalHeading(fn($record) => "View {$record->title}"),
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {

                        $product = Product::find($record);

                        FlashSaleProduct::where('product_id', $record->id)->delete();
                        OrderedProduct::where('product_id', $record->id)->delete();
                        CollectionWithProduct::where('product_id', $record->id)->delete();

                        $product_inventories = UpdatedInventory::where('product_id', $record->id)->get();
                        ProductCategory::where('product_id', $record->id)->delete();

                        foreach ($product_inventories as $inv) {
                            InventoryAttribute::where('inventory_id', $inv->id)->delete();
                        }

                        Cart::where('product_id', $record->id)->delete();
                        CompareList::where('product_id', $record->id)->delete();

                        UpdatedInventory::where('product_id', $record->id)->delete();

                        $description_images = WysiwygImage::where('item_id', $record->id)->get();
                        foreach ($description_images as $di) {
                            $di->delete();
                            FileHelper::deleteFile($di->image);
                        }

                        $product_images = ProductImage::where(['product_id' => $record->id])->get();

                        foreach ($product_images as $img) {
                            $img->delete();
                            FileHelper::deleteFile($img->image);
                        }

                        UserWishlist::where('product_id', $record->id)->delete();

                        $reviewImages = ReviewImage::leftJoin('rating_reviews', 'review_images.rating_review_id', '=', 'rating_reviews.id')
                            ->where('rating_reviews.product_id', $record->id);

                        $rimages = $reviewImages->get();
                        foreach ($rimages as $img) {
                            FileHelper::deleteFile($img->image);
                        }

                        $reviewImages->delete();

                        RatingReview::where('product_id', $record->id)->delete();
                        ProductLang::where('product_id', $record->id)->delete();

                        if ($product->delete()) {
                            FileHelper::deleteFile($product->video);
                            FileHelper::deleteFile($product->video_thumb);

                            Notification::make()
                                ->title('Deleted')
                                ->body('Product Successfully Deleted.')
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
                })
            ])
            ->headerActions([
                ExportAction::make()->exports([
                    ExcelExport::make('table')
                        ->withFilename(date('Y-m-d') . ' - products')
                        ->fromTable()
                        ->askForWriterType(),
                    // ExcelExport::make('form')->fromForm()
                    //     ->withFilename(date('Y-m-d') . ' - products')
                    //     ->askForWriterType(),
                ])
            ])
            ->deferLoading()
            ->paginationPageOptions(\App\Helper\Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            ProductImagesRelationManager::class,
            LanguageRelationManager::class,
//            VariantRelationManager::class,
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Seller\Resources\ProductResource\Pages\ListProducts::route('/'),
            'create' => \App\Filament\Seller\Resources\ProductResource\Pages\CreateProduct::route('/create'),
            'edit' => \App\Filament\Seller\Resources\ProductResource\Pages\EditProduct::route('/{record}/edit'),
            'view' => \App\Filament\Seller\Resources\ProductResource\Pages\ViewProduct::route('/{record}/view'),
        ];
    }

    public static function productVarient(): array
    {

        $checkbox = [];
        $query = Attribute::query()->get();

        foreach ($query as $attr) {
            $checkbox[] = Forms\Components\CheckboxList::make($attr->title)
                ->options(fn() => AttributeValue::where('attribute_id', $attr->id)->pluck('title', 'id'))
                ->columns(['sm' => 2, 'md' => 3, 'lg' => 5])
                ->inlineLabel()
                ->afterStateUpdated(fn(Forms\Set $set, $state) => $set('Test', $state))
                ->live(onBlur: true)
                ->gridDirection('row');
        }

        return $checkbox;
    }


    public static function getNavigationBadge(): ?string
    {
        $count = 0;
        if(Auth::check()){
            $adminId = auth()->user()->id;
            $count = Product::query()->where('admin_id', $adminId)->count();
        }
        return $count;
    }
}
