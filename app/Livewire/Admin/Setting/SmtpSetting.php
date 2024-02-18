<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Artisan;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class SmtpSetting extends Component implements HasForms
{
    use InteractsWithForms;

    public string $smtpHost;
    public string $smtpPort;
    public string $smtpUsername;
    public string $smtpPassword;
    public string $smtpEncryption;
    public string $mailFrom;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'smtpHost' => env('MAIL_HOST'),
            'smtpPort' => env('MAIL_PORT'),
            'smtpUsername' => env('MAIL_USERNAME'),
            'smtpPassword' => env('MAIL_PASSWORD'),
            'smtpEncryption' => env('MAIL_ENCRYPTION'),
            'mailFrom' => env('MAIL_FROM_ADDRESS'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('smtpHost')->label('SMTP Host')
                    ->prefixIcon('tabler-link'),
                Grid::make(2)->schema([
                    TextInput::make('smtpPort')->label('SMTP Port')
                        ->prefixIcon('tabler-plug-connected'),
                    TextInput::make('smtpEncryption')->label('SMTP Encryption')
                        ->prefixIcon('tabler-shield-lock'),
                ]),
                TextInput::make('smtpUsername')->label('SMTP Username')
                    ->prefixIcon('tabler-user'),
                TextInput::make('smtpPassword')
                    ->password()
                    ->label('SMTP Password')
                    ->prefixIcon('tabler-lock'),
                TextInput::make('mailFrom')->label('Mail From')
                    ->prefixIcon('tabler-mail'),
            ])->statePath('data');
    }

    public function submit(): void
    {
        try {
            $db = [
                "MAIL_HOST" => $this->data['smtpHost'],
                "MAIL_PORT" => $this->data['smtpPort'],
                "MAIL_USERNAME" => $this->data['smtpUsername'],
                "MAIL_PASSWORD" => $this->data['smtpPassword'],
                "MAIL_ENCRYPTION" => $this->data['smtpEncryption'],
                "MAIL_FROM_ADDRESS" => $this->data['mailFrom'],
            ];
            // dd($db);
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);
            if (file_exists($envPath)) {
                foreach ($db as $key => $value) {
                    $envContent = preg_replace('/^' . $key . '=.*/m', $key . '=' . $value, $envContent);
                }
            }
            file_put_contents($envPath, $envContent);
            Artisan::call('optimize:clear');
            Notification::make()
                ->title('Successfully')
                ->success()
                ->body('SMTP Setting updated successfully')
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body($th->getMessage())
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.admin.setting.smtp-setting');
    }
}
