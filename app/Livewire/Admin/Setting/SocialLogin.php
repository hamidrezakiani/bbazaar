<?php

namespace App\Livewire\Admin\Setting;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Artisan;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class SocialLogin extends Component implements HasForms
{
    use InteractsWithForms;

    //Social login
    public string $googleClientId;
    public string $googleClientSecret;
    public string $facebookClientId;
    public string $facebookClientSecret;

    public ?array $data = [];
    public $setting;

    public function mount(): void
    {
        $this->setting = \App\Models\Setting::first();
        $this->form->fill([
            'googleClientId' => env('GOOGLE_CLIENT_ID'),
            'googleClientSecret' => env('GOOGLE_CLIENT_SECRET'),
            'google_login' => $this->setting->google_login,
            'facebookClientId' => env('FACEBOOK_CLIENT_ID'),
            'facebookClientSecret' => env('FACEBOOK_CLIENT_SECRET'),
            'facebook_login' => $this->setting->facebook_login
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('google_login')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off')
                    ->reactive(),
                TextInput::make('googleClientId')
                    ->password()
                    ->disabled(fn(Get $get) => $get('google_login') == null)
                    ->label('Google Client Id'),
                TextInput::make('googleClientSecret')->password()->label('Google Client Secret')
                    ->disabled(fn(Get $get) => $get('google_login') == null),
                Toggle::make('facebook_login')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off')
                    ->reactive(),
                TextInput::make('facebookClientId')->password()->label('Facebook Client Id')
                    ->disabled(fn(Get $get) => $get('facebook_login') == null),
                TextInput::make('facebookClientSecret')->password()->label('Facebook Client Secret')
                    ->disabled(fn(Get $get) => $get('facebook_login') == null),
            ])->statePath('data');
    }

    public function submit(): void
    {
        try {
            $db = [
                "GOOGLE_CLIENT_ID" => $this->data['googleClientId'],
                "GOOGLE_CLIENT_SECRET" => $this->data['googleClientSecret'],
                "FACEBOOK_CLIENT_ID" => $this->data['facebookClientId'],
                "FACEBOOK_CLIENT_SECRET" => $this->data['facebookClientSecret']
            ];
            $envPath = base_path('env.php');
            if (file_exists($envPath)) {
                Artisan::call('optimize:clear');
                foreach ($db as $key => $value) {
                    file_put_contents($envPath, str_replace(
                        $key . '=' . env($key),
                        $key . '=' . $value,
                        file_get_contents($envPath)
                    ));
                }
                $this->setting->update([
                    'google_login' => $this->data['google_login'],
                    'facebook_login' => $this->data['facebook_login']
                ]);
                Notification::make()
                    ->title('Successfully')
                    ->success()
                    ->body('Social Login Credentials updated successfully')
                    ->send();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function render(): View
    {
        return view('livewire.admin.setting.social-login');
    }
}
