<?php

namespace App\Filament\Admin\Resources\RegisterUserResource\Pages;

use Filament\Actions;
use App\Models\Customer;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\RegisterUserResource;

class ListRegisterUsers extends ListRecords
{
    protected static string $resource = RegisterUserResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
    
    public function getTableQuery(): ?Builder
    {
        return Customer::query()->latest();
    }
}
