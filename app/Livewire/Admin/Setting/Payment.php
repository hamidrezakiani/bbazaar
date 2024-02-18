<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Wallo\FilamentSelectify\Components\ToggleButton;

class Payment extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {

        $payment = \App\Models\Payment::first();
        $this->form->fill([
            'admin_id' => $payment->admin_id,
            'cash_on_delivery' => $payment->cash_on_delivery,
            'stripe' => $payment->stripe,
            'stripe_key' => $payment->stripe_key,
            'stripe_secret' => $payment->stripe_secret,
            'paypal' => $payment->paypal,
            'paypal_key' => $payment->paypal_key,
            'paypal_secret' => $payment->paypal_secret,
            'razorpay' => $payment->razorpay,
            'razorpay_key' => $payment->razorpay_key,
            'razorpay_secret' => $payment->razorpay_secret,
            'flutterwave' => $payment->flutterwave,
            'fw_environment' => $payment->fw_environment,
            'fw_public_key' => $payment->fw_public_key,
            'fw_secret_key' => $payment->fw_secret_key,
            'fw_encryption_key' => $payment->fw_encryption_key,
            'iyzico_payment' => $payment->iyzico_payment,
            'ip_base_url' => $payment->ip_base_url,
            'ip_api_key' => $payment->ip_api_key,
            'ip_secret_key' => $payment->ip_secret_key,
            'bank' => $payment->bank,
            'bank_name' => $payment->bank_name,
            'account_name' => $payment->account_name,
            'branch_name' => $payment->branch_name,
            'account_number' => $payment->account_number
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Toggle::make('cash_on_delivery')
                ->offIcon('tabler-x')
                ->onIcon('tabler-check')
                ->default(true)
                ->columnSpan(1),
            Section::make('PayPal')->schema([
                ToggleButton::make('paypal')
                    ->onColor('primary')
                    ->offLabel('Inactive')
                    ->onLabel('Active')
                    ->default(true)
                    ->hiddenLabel()
                    ->reactive()
                    ->columnSpan(1),
                TextInput::make('paypal_key')
                    ->rules(['required_if:data.paypal,true'])
                    ->disabled(fn(Get $get) => $get('paypal') == 0),
                TextInput::make('paypal_secret')
                    ->rules(['required_if:data.paypal,true'])
                    ->disabled(fn(Get $get) => $get('paypal') == 0),
            ])->collapsible()->collapsed()->columns(1),
            Section::make('Stripe')->schema([
                ToggleButton::make('stripe')
                    ->onColor('primary')
                    ->offLabel('Inactive')
                    ->onLabel('Active')
                    ->default(true)
                    ->reactive()
                    ->hiddenLabel()
                    ->columnSpan(1),
                TextInput::make('stripe_key')
                    ->rules(['required_if:data.stripe,true'])
                    ->disabled(fn(Get $get) => $get('stripe') == 0),
                TextInput::make('stripe_secret')
                    ->rules(['required_if:data.stripe,true'])
                    ->disabled(fn(Get $get) => $get('stripe') == 0),
            ])->collapsible()->collapsed()->columns(1),
            Section::make('Razorpay')->schema([
                ToggleButton::make('razorpay')
                    ->onColor('primary')
                    ->offLabel('Inactive')
                    ->onLabel('Active')
                    ->reactive()
                    ->default(true)
                    ->hiddenLabel()
                    ->columnSpan(1),
                TextInput::make('razorpay_key')
                    ->rules(['required_if:data.razorpay,true'])
                    ->disabled(fn(Get $get) => $get('razorpay') == 0),
                TextInput::make('razorpay_secret')
                    ->rules(['required_if:data.razorpay,true'])
                    ->disabled(fn(Get $get) => $get('razorpay') == 0),
            ])->collapsible()->collapsed()->columns(1),
            Section::make('Flutter Wave')->schema([
                ToggleButton::make('flutterwave')
                    ->onColor('primary')
                    ->offLabel('Inactive')
                    ->onLabel('Active')
                    ->default(true)
                    ->reactive()
                    ->hiddenLabel()
                    ->columnSpan(1),

                Select::make('fw_environment')->options([
                    'development' => 'Development',
                    'production' => 'Production'
                ])->disabled(fn(Get $get) => $get('flutterwave') == 0)
                    ->rules(['required_if:data.flutterwave,true']),
                TextInput::make('fw_public_key')
                    ->disabled(fn(Get $get) => $get('flutterwave') == 0)
                    ->rules(['required_if:data.flutterwave,true']),
                TextInput::make('fw_secret_key')
                    ->disabled(fn(Get $get) => $get('flutterwave') == 0)
                    ->rules(['required_if:data.flutterwave,true']),
                TextInput::make('fw_encryption_key')
                    ->disabled(fn(Get $get) => $get('flutterwave') == 0)
                    ->rules(['required_if:data.flutterwave,true']),

            ])->collapsible()->collapsed()->columns(1),
            Section::make('Iyzico Payment')->schema([
                ToggleButton::make('iyzico_payment')
                    ->onColor('primary')
                    ->offLabel('Inactive')
                    ->onLabel('Active')
                    ->reactive()
                    ->hiddenLabel()
                    ->default(true)
                    ->columnSpan(1),
                TextInput::make('ip_base_url')
                    ->rules(['required_if:data.iyzico_payment,true'])
                    ->disabled(fn(Get $get) => $get('iyzico_payment') == 0),
                TextInput::make('ip_api_key')
                    ->rules(['required_if:data.iyzico_payment,true'])
                    ->disabled(fn(Get $get) => $get('iyzico_payment') == 0),
                TextInput::make('ip_secret_key')
                    ->rules(['required_if:data.iyzico_payment,true'])
                    ->disabled(fn(Get $get) => $get('iyzico_payment') == 0),

            ])->collapsible()->collapsed()->columns(1),
            Section::make('Bank Payment')->schema([
                ToggleButton::make('bank')
                    ->onColor('primary')
                    ->offLabel('Inactive')
                    ->onLabel('Active')
                    ->reactive()
                    ->hiddenLabel()
                    ->default(true)
                    ->columnSpan(1),
                TextInput::make('bank_name')
                    ->rules(['required_if:data.bank,true'])
                    ->disabled(fn(Get $get) => $get('bank') == 0),
                TextInput::make('account_name')
                    ->rules(['required_if:data.bank,true'])
                    ->disabled(fn(Get $get) => $get('bank') == 0),
                TextInput::make('branch_name')
                    ->rules(['required_if:data.bank,true'])
                    ->disabled(fn(Get $get) => $get('bank') == 0),
                TextInput::make('account_number')
                    ->rules(['required_if:data.bank,true'])
                    ->disabled(fn(Get $get) => $get('bank') == 0),
            ])->collapsible()->collapsed()->columns(1),

            Hidden::make('admin_id')->dehydrateStateUsing(fn() => Auth::user()->id)
        ])->statePath('data');
    }

    public function submit(): void
    {
        $this->validate();
        $payment = \App\Models\Payment::first();
        $payment->update($this->form->getState());
        Notification::make()->title('Success')
            ->body('Payment Method Update Successfully')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.admin.setting.payment');
    }
}
