<?php

namespace App\Filament\Admin\Pages;

use App\Models\Helper\FileHelper;
use App\Models\Language;
use App\Models\SiteSetting;
use App\Models\SiteSettingLang;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SiteSettings extends Page implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static ?string $navigationGroup = 'UI';
    protected static string $view = 'filament.admin.pages.site-settings';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Site Setting';

    public ?array $data = [];
    public ?SiteSetting $record = null;

    public function getTitle(): string|Htmlable
    {
        return static::$title;
    }

    public function mount(): void
    {
        $this->record = SiteSetting::where('admin_id', auth()->user()->id)->first();
        $this->fillForm();
    }

    public function fillForm(): void
    {
        $data = $this->record->attributesToArray();
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
                Section::make('Setting Form')->schema([
                    TextInput::make('site_name')
                        ->required(),
                    TextInput::make('site_url')
                        ->prefixIcon('tabler-link')
                        ->required()
                        ->url(),
                    TextInput::make('meta_title')->required(),
                    Textarea::make('meta_description')->required(),
                    TextInput::make('copyright_text')
                        ->prefixIcon('tabler-copyright')
                        ->required(),
                    Grid::make(2)->schema([
                        ColorPicker::make('primary_color')
                            ->prefixIcon('tabler-color-swatch')
                            ->label('Primary Color')
                            ->required(),
                        ColorPicker::make('primary_hover_color')
                            ->label('Primary Hover Color')
                            ->prefixIcon('tabler-color-swatch')
                            ->required(),
                    ])
                ])->aside(),
                Section::make('Header Logo')->schema([
                    FileUpload::make('header_logo')
                        ->label('Header Logo')
                        ->hiddenLabel()
                        ->disk('public')
                        ->image()
                        ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                        ->getUploadedFileNameForStorageUsing(
                            function (TemporaryUploadedFile $file): string {
                                $image_info = FileHelper::uploadToLocalFilament($file, 'header_logo');
                                return $image_info['name'];
                            }),
                ])->aside(),
                Section::make('Footer Logo')->schema([
                    FileUpload::make('footer_logo')
                        ->label('Footer Logo')
                        ->hiddenLabel()
                        ->disk('public')
                        ->image()
                        ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                        ->getUploadedFileNameForStorageUsing(
                            function (TemporaryUploadedFile $file): string {
                                $image_info = FileHelper::uploadToLocalFilament($file, 'footer_logo');
                                return $image_info['name'];
                            }),
                ])->aside(),
                Section::make('Email Logo')->schema([
                    FileUpload::make('email_logo')
                        ->label('Email Logo')
                        ->hiddenLabel()
                        ->disk('public')
                        ->image()
                        ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                        ->getUploadedFileNameForStorageUsing(
                            function (TemporaryUploadedFile $file): string {
                                $image_info = FileHelper::uploadToLocalFilament($file, 'email_logo');
                                return $image_info['name'];
                            }),
                ])->aside(),
        ])
            ->model($this->record)
            ->statePath('data')
            ->operation('edit');
    }

    public function update(): void
    {
        $this->record->update($this->form->getState());
        Notification::make()
            ->title('Success')
            ->body('Site Setting Updated Successfully')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SiteSettingLang::query()->where('site_setting_id', $this->record->id))
            ->columns([
                TextColumn::make('site_name'),
                TextColumn::make('meta_title'),
                TextColumn::make('meta_description'),
                TextColumn::make('copyright_text')
                ->label('Copyright Text'),
                TextColumn::make('lang')
                ->label('Language'),
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()->form($this->getLanguageForm())
            ])->actions([
                \App\Helper\Actions::EditAction()->form($this->getLanguageForm()),
                \App\Helper\Actions::DeleteAction()
            ]);
    }

    public function getLanguageForm():array{
        return [
            Select::make('lang')
                ->options(Language::all()->pluck('name', 'code'))
                ->searchable()
                ->distinct()
                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
            Group::make()->schema([
                TextInput::make('site_name')->required(),
                TextInput::make('meta_title')->required(),
            ])->columns(2),
            Textarea::make('meta_description')->required(),
            TextInput::make('copyright_text')->required(),
        ];
    }
}
