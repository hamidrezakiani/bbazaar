<?php

namespace App\Helper;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Forms;

class FormInput
{

    public static function CategoryRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('category')
            ->visible(function (Forms\Get $get) {
                if ($get('source_type') == 1) {
                    return true;
                } else {
                    return false;
                }
            })
            ->relationship('source_categories')
            ->label('Category')
            ->schema([
                Forms\Components\Select::make('category_id')->options(Category::where('status', 1)->pluck('title', 'id'))
                    ->label('Category')
                    ->searchable()
            ])->addActionLabel('Add Category')
            ->itemLabel(fn(array $state): ?string => Category::find($state['category_id'])?->title ?? null)
            ->grid(3);

    }

    public static function SubCategoryRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('sub_category')
            ->visible(function (Forms\Get $get) {
                if ($get('source_type') == 2) {
                    return true;
                } else {
                    return false;
                }
            })
            ->relationship('source_sub_categories')
            ->label('Sub Category')
            ->schema([
                Forms\Components\Select::make('sub_category_id')
                    ->options(SubCategory::where('status', 1)->pluck('title', 'id'))
                    ->label('Sub Category')
                    ->searchable()
            ])->addActionLabel('Add Sub Category')
            ->itemLabel(fn(array $state): ?string => SubCategory::find($state['sub_category_id'])?->title ?? null)
            ->grid(3);
    }

    public static function tags(): Forms\Components\TagsInput
    {
        return Forms\Components\TagsInput::make('tags')
            ->visible(function (Forms\Get $get) {
                if ($get('source_type') == 3) {
                    return true;
                } else {
                    return false;
                }
            });
    }

    public static function BrandRepeater(){
        return Forms\Components\Repeater::make('brand')
            ->visible(function (Forms\Get $get) {
                if ($get('source_type') == 4) {
                    return true;
                } else {
                    return false;
                }
            })
            ->relationship('source_brands')
            ->label('Brand')
            ->schema([
                Forms\Components\Select::make('brand_id')->options(Brand::where('status', 1)->pluck('title', 'id'))
                    ->label('Brand')
                    ->searchable()
            ])->addActionLabel('Add Brand')
            ->itemLabel(fn(array $state): ?string => Brand::find($state['brand_id'])?->title ?? null)
            ->grid(3);
    }

    public static function ProductRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('product')
            ->relationship('source_products')
            ->label('Products')
            ->schema([
                Forms\Components\Select::make('product_id')
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
                    ->allowHtml()
                    ->label('Product')
                    ->searchable(),
            ])->addActionLabel('Add Product')
            ->itemLabel(fn(array $state): ?string => Product::find($state['product_id'])?->title ?? null)
            ->grid(2);
    }

    public static function Url(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('url')->prefixIcon('tabler-link')->url();
    }
}
