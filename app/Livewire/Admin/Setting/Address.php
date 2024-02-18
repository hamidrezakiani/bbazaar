<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use Filament\Forms\Components\Grid;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;

class Address extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::first();
        $this->form->fill([
            'email' => $setting->email,
            'phone' => $setting->phone,
            'address_1' => $setting->address_1,
            'address_2' => $setting->address_2,
            'city' => $setting->city,
            'state' => $setting->state,
            'zip' => $setting->zip,
            'country' => $setting->country,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')->required()->prefixIcon('tabler-mail'),
                TextInput::make('phone')->required()->prefixIcon('tabler-phone'),
                TextInput::make('address_1')->required()->prefixIcon('tabler-map'),
                TextInput::make('address_2')->prefixIcon('tabler-map'),
                Grid::make(2)->schema([
                    TextInput::make('city')->required()->prefixIcon('tabler-map-pin'),
                    TextInput::make('state')->required()->prefixIcon('tabler-map-2'),
                    TextInput::make('zip')->prefixIcon('tabler-hash'),
                    TextInput::make('country')->required()->prefixIcon('tabler-world'),
                ]),
            ])->columns(2)
            ->statePath('data');
    }

    public function submit(): void
    {
        $setting = Setting::first();
        $setting->update($this->form->getState());
        Notification::make()->title('Success')
            ->body('Address Update Successfully')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.admin.setting.address');
    }
}
