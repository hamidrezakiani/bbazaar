<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\ProductResource;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        return [
            Actions\Action::make('Edit')
                ->icon('tabler-edit')
                ->url(fn () => ProductResource::getUrl('edit',[$record]))
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
