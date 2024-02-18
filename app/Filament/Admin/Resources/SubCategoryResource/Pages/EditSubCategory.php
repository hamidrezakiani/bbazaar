<?php

namespace App\Filament\Admin\Resources\SubCategoryResource\Pages;

use App\Filament\Admin\Resources\SubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubCategory extends EditRecord
{
    protected static string $resource = SubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
