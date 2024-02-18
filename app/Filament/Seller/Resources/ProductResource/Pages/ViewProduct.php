<?php

namespace App\Filament\Seller\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Seller\Resources\ProductResource;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }


    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function updated()
    {
        $this->dispatch('updateAuditsRelationManager');
    }

}
