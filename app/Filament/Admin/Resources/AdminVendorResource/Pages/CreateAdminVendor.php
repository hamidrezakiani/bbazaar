<?php

namespace App\Filament\Admin\Resources\AdminVendorResource\Pages;

use App\Filament\Admin\Resources\AdminVendorResource;
use App\Models\Helper\Utils;
use App\Models\Store;
use Filament\Resources\Pages\CreateRecord;

class CreateAdminVendor extends CreateRecord
{
    protected static string $resource = AdminVendorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
    protected function afterCreate():void{
        $user = $this->record;
        $store['name'] = $user->name;
        $store['slug'] = $user->username;
        $store['admin_id'] = $user->id;
        $existingSlug = Store::where('slug', $store['slug'])->first();
        if ($existingSlug) {
            $store['slug'] = $user->username . Utils::generateRandomString(5);
        }
        Store::create($store);
    }
}
