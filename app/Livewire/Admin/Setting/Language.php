<?php

namespace App\Livewire\Admin\Setting;

use Filament\Forms\Components\Radio;
use Filament\Forms\Set;
use Filament\Tables\Actions\ActionGroup;
use Livewire\Component;
use Filament\Tables\Table;
use App\Models\GlobalLanguage;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use App\Models\Language as ModelsLanguage;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Wallo\FilamentSelectify\Components\ToggleButton;

class Language extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;
    public null|int $previousDefault = 0;

    public function table(Table $table): Table
    {
        return $table
            ->query(ModelsLanguage::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('code'),
                TextColumn::make('direction'),
                TextColumn::make('predefined'),
                IconColumn::make('default')
                    ->icon(fn(string $state): string => match ($state) {
                        '0' => 'tabler-x',
                        '1' => 'tabler-circle-check',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                    })->formatStateUsing(fn($state): string => $state === 1 ? 'Active' : 'Inactive'),
                TextColumn::make('created_at'),
            ])
            ->actions([
                    \App\Helper\Actions::EditAction()
                        ->form(self::getlanguageForm())
                        ->modalWidth('lg')
                        ->modalHeading(fn($record) => "Edit {$record->name}")
                        ->before(function (array $data) {
                            $language = \App\Models\Language::where('default',1)->first();
                            $this->previousDefault = $language?->id;
                        })
                        ->after(function($record){
                            \App\Models\Language::where('id',$this->previousDefault)->update([
                                'default' => 0
                            ]);
                        }),
                \App\Helper\Actions::DeleteAction('Delete'),
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()->label('Language')
                    ->form(self::getlanguageForm())
                    ->modalWidth('lg')
                    ->modalHeading('Add Language')
                    ->createAnother(false)
                    ->before(function (array $data) {
                        $language = \App\Models\Language::where('default',1)->first();
                        $this->previousDefault = $language->id;
                    })
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.admin.setting.language');
    }

    public static function getlanguageForm(): array
    {
        return [
            Select::make('name')
                ->live(onBlur: true)
                ->searchable()
                ->options(GlobalLanguage::all()->pluck('name', 'code'))
                ->afterStateUpdated(fn($state, Set $set) => $set('code', $state))
                ->required(),
            TextInput::make('code')->readOnly()->required(),
            Radio::make('direction')->options([
                'ltr' => 'LTR',
                'rtl' => 'RTL'
            ]),
            Checkbox::make('default')
                ->default(0)
                ->inline(false),
            Toggle::make('status')->inline(false)
                ->onIcon('tabler-eye')
                ->offIcon('tabler-eye-off'),
        ];
    }
}
