<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\StoreResource\Pages;
use App\Filament\Admin\Resources\Admin\StoreResource\RelationManagers;
use App\Filament\Admin\Resources\StoreResource\RelationManagers\LanguageRelationManager;
use App\Helper\Setting;
use App\Models\Helper\FileHelper;
use App\Models\Store;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;
    protected static ?string $navigationIcon = 'tabler-building-store';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Logo')
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
                Forms\Components\Section::make('Basic Info')
                    ->description('Add some basic info about your shop from here')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Forms\Set $set, $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')->required(),
                    ])->aside(),
//                FormInput\Components\Section::make('Shop Address')
//                    ->description('Add your physical shop address from here')
//                    ->schema([
//                        FormInput\Components\TextInput::make('google_map')->label('Set location from map'),
//                        FormInput\Components\TextInput::make('country'),
//                        FormInput\Components\TextInput::make('city'),
//                        FormInput\Components\TextInput::make('state'),
//                        FormInput\Components\TextInput::make('zip'),
//                        FormInput\Components\TextInput::make('street'),
//                    ])->aside(),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ImageColumn::make('image')
                        ->label('Profile Image')
                        ->disk('public')
                        ->columnSpanFull()
                        ->grow(false),
                ]),
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('name')
                        ->color('gray')
                        ->size(TextColumnSize::Large)
                        ->formatStateUsing(fn($state) => new HtmlString("<b>Shop Name:</b> $state"))
                        ->searchable()
                        ->sortable()
                        ->wrap(),
                    Tables\Columns\TextColumn::make('status')
                        ->badge()
                        ->formatStateUsing(fn($state) => $state == 1 ? 'Active' : 'Inactive'),
                    Tables\Columns\TextColumn::make('followers')
                        ->color(Color::Emerald)
                        ->formatStateUsing(fn ($state) => $state !== null ? 'Followers: '.$state->count() : 'Followers: 0')
                        ->badge(),
                ]),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\ToggleColumn::make('status'),
                    Tables\Columns\TextColumn::make('products')
                        ->color('gray')
                        ->columnSpanFull()
                        ->formatStateUsing(fn($state) => new HtmlString("<b>Total Products: </b>".$state->products->count()))
                        ->searchable(),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('whatsapp_number')
                            ->color('gray')
                            ->columnSpanFull()
                            ->formatStateUsing(fn($state) => new HtmlString("<b>WhatsApp:</b> $state"))
                            ->searchable(),
                    ]),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('user.name')
                            ->color('gray')
                            ->columnSpanFull()
                            ->formatStateUsing(fn($state) => new HtmlString("<b>User:</b> $state"))
                            ->searchable(),
                    ]),
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('created_at')
                            ->label('Date')
                            ->formatStateUsing(fn($state) => new HtmlString("<b>Created:</b> " . Setting::dateTime($state)))
                            ->color('gray'),
                    ]),
                ])->extraAttributes(['style' => 'background-color:#fff'])->collapsible(),


//                Tables\Columns\Layout\Split::make([
//                    Tables\Columns\TextColumn::make('name')
//                        ->weight(FontWeight::Bold)
//                        ->searchable(),
//                    Tables\Columns\TextColumn::make('admin_id')
//                        ->badge()
//                        ->label('Commission')
//                        ->formatStateUsing(fn($state) => User::find($state)?->commission)
//                        ->sortable(),
//                ]),
//
//                Panel::make([
//                    View::make('filament.components.stores')
//                ]),
//                Tables\Columns\Layout\Split::make([
//                    Tables\Columns\ViewColumn::make('id')
//                    ->view('filament.components.stores'),
//                    Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
//                        ->collection('store-logo')
//                        ->circular(),
//                    Tables\Columns\TextColumn::make('name')
//                        ->searchable()
//                        ->sortable(),
//                    Tables\Columns\TextColumn::make('slug'),
//                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
            ]);
    }


    public static function getRelations(): array
    {
        return [
            LanguageRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\StoreResource\Pages\ListStores::route('/'),
            'create' => \App\Filament\Admin\Resources\StoreResource\Pages\CreateStore::route('/create'),
            'edit' => \App\Filament\Admin\Resources\StoreResource\Pages\EditStore::route('/{record}/edit'),
        ];
    }

    public static function getPluralLabel(): ?string
    {
        return 'Stores';
    }

    public static function getLabel(): ?string
    {
        return 'Store';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
