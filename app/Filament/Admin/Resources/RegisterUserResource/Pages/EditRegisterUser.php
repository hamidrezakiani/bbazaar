<?php

namespace App\Filament\Admin\Resources\RegisterUserResource\Pages;

use App\Filament\Admin\Resources\RegisterUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegisterUser extends EditRecord
{
    protected static string $resource = RegisterUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
