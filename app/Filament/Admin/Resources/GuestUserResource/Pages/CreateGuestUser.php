<?php

namespace App\Filament\Admin\Resources\GuestUserResource\Pages;

use App\Filament\Admin\Resources\GuestUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuestUser extends CreateRecord
{
    protected static string $resource = GuestUserResource::class;
}
