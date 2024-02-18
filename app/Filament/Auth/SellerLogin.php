<?php
namespace App\Filament\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Pages\Auth\Login as BaseAuth;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;

class SellerLogin extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }


    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException) {
            throw ValidationException::withMessages([
                'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }
    }
}
