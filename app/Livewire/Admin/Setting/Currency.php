<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Setting;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Squire\Models\Currency as SCurrency;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Actions\CreateAction;

class Currency extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public ?array $data = [];

    public function mount(): void
    {
        $currency = Setting::first();
        $this->form->fill([
            'currency' => $currency->currency,
            'currency_icon' => $currency->currency_icon,
            'currency_position' => $currency->currency_position,
            'exchange_rate' => $currency->exchange_rate,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(\App\Models\Currency::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('code'),
                TextColumn::make('symbol'),
                TextColumn::make('position'),
                TextInputColumn::make('price'),
                TextColumn::make('symbol'),
            ])->actions([
                \App\Helper\Actions::EditAction()
                    ->form(self::getCurrencyForm())
                        ->modalHeading(fn ($record):string => 'Edit '.$record->name)
                    ->modalWidth('lg')
            ])->headerActions([
                CreateAction::make()
                    ->label('Add Currency')
                    ->icon('tabler-currency-dollar')
                    ->form(self::getCurrencyForm())
                    ->createAnother(false)
                    ->modalWidth('lg')
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('currency')
                    ->searchable()
                    ->live(onBlur: true)
                    ->getSearchResultsUsing(fn(string $query) => SCurrency::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                    ->getOptionLabelUsing(fn($value): ?string => SCurrency::find($value)?->getAttribute('name'))
                    ->dehydrateStateUsing(fn($state) => Str::upper($state))
                    ->afterStateUpdated(function ($state, Set $set) {
                        $icon = SCurrency::find($state)?->getAttribute('symbol_native');
                        $set('currency_icon', $icon);
                    })
                    ->required(),
                TextInput::make('currency_icon')->readOnly(),
                Select::make('currency_position')->options([
                    1 => 'Left',
                    2 => 'Right'
                ]),
                TextInput::make('exchange_rate')
                    ->required()
                    ->numeric(),
            ])->columns(2)
            ->statePath('data');
    }

    public function submit(): void
    {
        $setting = Setting::first();
        $setting->update($this->form->getState());
        Notification::make()->title('Success')
            ->body('Currency Update Successfully')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.admin.setting.currency');
    }

    public static function getCurrencyForm(): array
    {
        return [
            TextInput::make('name')
                ->hiddenLabel()
                ->prefix('Currency')
                ->required(),
            TextInput::make('code')
                ->hiddenLabel()
                ->prefix('Code')
                ->required(),
            TextInput::make('symbol')
                ->hiddenLabel()
                ->prefix('Symbol')
                ->required(),
            TextInput::make('position')
                ->hiddenLabel()
                ->prefix('Position')
                ->numeric()
                ->required(),
            TextInput::make('price')
                ->hiddenLabel()
                ->prefix('Price')
                ->numeric()
                ->required(),
            Hidden::make('admin_id')->dehydrateStateUsing(fn () => Auth::user()->id)

        ];
    }
}
