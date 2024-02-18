<?php

namespace App\Helper;

use Illuminate\Database\Eloquent\Model;

class Select
{
    public static function getCleanProductOption(Model $model): string
    {
        return view('filament.components.select-product')
            ->with('name', $model?->title)
            ->with('image', $model?->image)
            ->with('qty', $model?->qty)
            ->render();
    }

    public static function getCleanBrandOption(Model $model): string
    {
        return view('filament.components.select-brand')
            ->with('name', $model?->title)
            ->with('image', $model?->image)
            ->with('id', $model?->id)
            ->render();
    }
    public static function getCleanCategoryOption(Model $model): string
    {
        return view('filament.components.select-category')
            ->with('name', $model?->title)
            ->with('image', $model?->image)
            ->with('id', $model?->id)
            ->render();
    }

    public static function getCleanSubCategoryOption(Model $model): string
    {
        return view('filament.components.select-sub-category')
            ->with('name', $model?->title)
            ->with('image', $model?->image)
            ->with('id', $model?->id)
            ->render();
    }
}
