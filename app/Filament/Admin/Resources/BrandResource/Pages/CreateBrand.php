<?php

namespace App\Filament\Admin\Resources\BrandResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\BrandResource;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateBrand extends CreateRecord
{

    protected static string $resource = BrandResource::class;

}
