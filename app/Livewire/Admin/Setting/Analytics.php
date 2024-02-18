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

class Analytics extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public $setting;

    public function mount(): void
    {
        $this->setting = \App\Models\Setting::first();
        $this->form->fill([
            'enable_ga' => $this->setting->enable_ga,
            'ga_id' => $this->setting->ga_id,
            'enable_pixel' => $this->setting->enable_pixel,
            'pixel_id' => $this->setting->pixel_id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('enable_ga')
                    ->label('Enable Google Analytics')
                    ->default(false)
                    ->offIcon('tabler-x')
                    ->onIcon('tabler-check')
                    ->live(onBlur: true),
                TextInput::make('ga_id')
                    ->rules(['required_if:data.enable_ga,true'])
                    ->prefixIcon('tabler-brand-google')
                    ->disabled(fn(Get $get) => $get('enable_ga') == null)
                    ->label('Google Analytics ID'),

                Toggle::make('enable_pixel')
                    ->label('Enable Facebook Pixel')
                    ->offIcon('tabler-x')
                    ->onIcon('tabler-check')
                    ->live(onBlur: true),
                TextInput::make('pixel_id')
                    ->prefixIcon('tabler-brand-facebook')
                    ->label('Facebook Pixel ID')
                    ->rules(['required_if:data.pixel_id,true'])
                    ->disabled(fn(Get $get) => $get('enable_pixel') == null),
            ])->statePath('data');
    }

    public function submit(): void
    {
        $this->validate();
        try {

            $this->setting->update([
                'enable_ga' => $this->data['enable_ga'],
                'ga_id' => $this->data['ga_id'],
                'enable_pixel' => $this->data['enable_pixel'],
                'pixel_id' => $this->data['pixel_id']
            ]);
            Notification::make()
                ->title('Successfully')
                ->success()
                ->body('Analytics updated successfully')
                ->send();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function render(): View
    {
        return view('livewire.admin.setting.social-login');
    }
}
