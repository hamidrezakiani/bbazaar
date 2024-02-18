<?php

namespace App\Filament\Admin\Resources\TaxRulesResource\Pages;

use App\Filament\Admin\Resources\TaxRulesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxRules extends EditRecord
{
    protected static string $resource = TaxRulesResource::class;

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
