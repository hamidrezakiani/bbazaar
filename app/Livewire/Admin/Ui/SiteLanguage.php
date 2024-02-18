<?php

namespace App\Livewire\Admin\Ui;

use App\Models\Language;
use App\Models\SiteSettingLang;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
class SiteLanguage extends Component implements HasForms,HasTable
{
    public $lang;
    public $language;
    use InteractsWithForms, InteractsWithTable;

//    public function mount(){
//        $this->language = SiteSettingLang::where('site_setting_id',$this->lang->id)->get();
//        $this->data = $this->language;
//    }

    public function render(){
        return view('livewire.admin.ui.site-language');
    }

    public function table(Table $table): Table
    {   return $table
        ->query(SiteSettingLang::query()->where('site_setting_id',1))
        ->columns([
            TextColumn::make('site_name'),
            TextColumn::make('meta_title')
            ->words(5)
            ->tooltip(fn ($state) => $state),
            TextColumn::make('meta_description')
                ->words(10)
                ->tooltip(fn ($state) => $state)
                ->extraHeaderAttributes(['style' => 'width:100%'])
                ->wrap(),
            TextColumn::make('lang')
        ])
        ->actions([
            EditAction::make('Edit')->form([
                Select::make('lang')
                    ->options(Language::all()->pluck('name','code'))
                    ->searchable(),
                TextInput::make('site_name')->required(),
                TextInput::make('meta_title')->required(),
                Textarea::make('meta_description')->required(),
                TextInput::make('copyright_text')->required(),
            ]),
            DeleteAction::make()
        ])
        ->headerActions([
            CreateAction::make()->form([
                Select::make('lang')
                    ->options(Language::all()->pluck('name','code'))
                ->searchable(),
                TextInput::make('site_name')->required(),
                TextInput::make('meta_title')->required(),
                Textarea::make('meta_description')->required(),
                TextInput::make('copyright_text')->required(),
                Hidden::make('site_setting_id')->default(1)
            ])->label('Language')->icon('tabler-circle-plus')
        ]);
    }

    public function form(Form $form): Form{

        return $form->schema([

        ])
            ->statePath('data')
            ->model($this->language);
    }



}
