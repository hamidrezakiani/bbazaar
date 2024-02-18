<?php

namespace App\Filament\Seller\Pages;

use App\Models\Helper\FileHelper;
use App\Models\Language;
use App\Models\Store;
use App\Models\StoreLang;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StoreSettings extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'tabler-settings';
    protected static string $view = 'filament.seller.pages.store-settings';
    protected static ?string $title = 'Setting';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 4;

    public ?array $data = [];
    public ?Store $record = null;

    public function getTitle(): string|Htmlable
    {
        return static::$title;
    }

    public function mount(): void
    {
        $this->record = Store::where('admin_id', auth()->user()->id)->first();
        $this->fillForm();
    }

    public function fillForm(): void
    {
        $data = $this->record->attributesToArray();
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Logo')
                    ->description('Upload your shop logo from here')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Image')
                            ->hiddenLabel()
                            ->disk('public')
                            ->image()
                            ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file): string {
                                    $image_info = FileHelper::uploadToLocalFilament($file, 'image');
                                    return $image_info['name'];
                                }),
                    ])->aside(),
                Section::make('Basic Info')
                    ->description('Add some basic info about your shop from here')
                    ->schema([
                        TextInput::make('name')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Forms\Set $set, $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')->required(),
                    ])->aside(),
                Forms\Components\Section::make('Store Setting')
                    ->description('Add your shop settings information from here')
                    ->schema([
                        Forms\Components\Toggle::make('whatsapp_btn')
                            ->label('WhatsApp Button')
                            ->onIcon('tabler-check')
                            ->offIcon('tabler-x')
                            ->reactive(),
                        Forms\Components\TextInput::make('whatsapp_number')
                            ->disabled(fn(Forms\Get $get): bool => $get('whatsapp_btn') == 0),
                        Forms\Components\TextInput::make('whatsapp_default_msg')
                            ->disabled(fn(Forms\Get $get): bool => $get('whatsapp_btn') == 0),
                    ])->aside(),
                Forms\Components\Section::make('SEO')
                    ->description('Add Store SEO from here')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')->required(),
                        Forms\Components\Textarea::make('meta_description')->required(),
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
            ->body('Store Setting Updated Successfully')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(StoreLang::query()->where('store_id', $this->record->id))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('meta_title'),
                TextColumn::make('meta_description')
                ->words(10)
                ->tooltip(fn ($state) =>  $state),
                TextColumn::make('lang'),
            ])
            ->actions([
                \App\Helper\Actions::EditAction()->form(self::getLanguageForm()),
                \App\Helper\Actions::DeleteAction()
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()->form(self::getLanguageForm())
            ]);
    }

    public function getLanguageForm(): array {
        return [
            Forms\Components\Select::make('lang')
                ->label('Language')
                ->searchable()
                ->options(Language::all()->pluck('name', 'code')),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('meta_title')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('meta_description')
                ->required()
                ->maxLength(255),
        ];
    }

}
