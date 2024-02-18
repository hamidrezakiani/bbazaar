<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Support\Colors\Color;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\ProductResource;
use Noxo\FilamentActivityLog\Extensions\LogEditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            Actions\Action::make('Create')
                ->icon('tabler-circle-plus')
            ->url(fn () => ProductResource::getUrl('create'))

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
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
