<?php

namespace App\Filament\Admin\Resources\Ui\BannerResource\Pages;

use App\Filament\Admin\Resources\Ui\BannerResource;
use Filament\Resources\Pages\ListRecords;

class ListBanners extends ListRecords
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
