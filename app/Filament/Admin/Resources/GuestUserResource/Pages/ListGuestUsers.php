<?php

namespace App\Filament\Admin\Resources\GuestUserResource\Pages;

use App\Models\GuestUser;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\GuestUserResource;

class ListGuestUsers extends ListRecords
{
    protected static string $resource = GuestUserResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
    
     public function getTableQuery(): ?Builder
    {
        return GuestUser::query()->latest();
    }
}
