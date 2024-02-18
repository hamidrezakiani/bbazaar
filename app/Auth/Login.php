<?php

namespace App\Auth;


use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuth {

    public function form(Form $form):Form {
        return $form->schema([
            $this->getLoginFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent()
        ])->statePath('data');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type =  filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        return [
            $login_type => $data['login'],
            'password' => $data['password']
        ];
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
