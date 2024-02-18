<?php

namespace App\Filament\Admin\Resources\TaxRulesResource\Pages;

use App\Filament\Admin\Resources\TaxRulesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaxRules extends ListRecords
{
    protected static string $resource = TaxRulesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('tabler-circle-plus')
            ->label('Tax Rule'),
        ];
    }
}
