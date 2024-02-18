<?php

namespace App\Filament\Admin\Resources\CategoryResource\Pages;

use App\Filament\Admin\Resources\CategoryResource;
use App\Filament\Resources\Category\ProductCategoryWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Category')
                ->icon('tabler-circle-plus')
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
//            \App\Filament\Admin\Resources\CategoryResource\Widgets\CategoryWidget::class
        ];
    }
}
