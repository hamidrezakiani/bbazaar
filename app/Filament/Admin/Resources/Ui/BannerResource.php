<?php

namespace App\Filament\Admin\Resources\Ui;

use App\Filament\Admin\Resources\Ui\BannerResource\RelationManagers\LanguageRelationManager;
use App\Models\Helper\FileHelper;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use App\Models\Brand;
use App\Models\Banner;
use App\Helper\Setting;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SubCategory;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Wallo\FilamentSelectify\Components\ToggleButton;
use App\Filament\Admin\Resources\Admin\Ui\BannerResource\Pages;
use App\Filament\Admin\Resources\Admin\Ui\BannerResource\RelationManagers;
use App\Helper\Select;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationGroup = 'UI';
    protected static ?string $navigationIcon = 'tabler-layout-board';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Banner Form')->schema([
                    Forms\Components\TextInput::make('title')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->required(),
                    Forms\Components\TextInput::make('slug')
                        ->dehydrated()
                        ->required()
                        ->unique(Banner::class, 'slug', ignoreRecord: true),
                    Forms\Components\Select::make('source_type')
                        ->required()
                        ->reactive()
                        ->options(Setting::sourceType()),
                    Forms\Components\Group::make()
                        ->schema(function (Forms\Get $get) {
                            $type = $get('source_type');
                            $field = [];
                            if($type == 1){
                                $field[] = \App\Helper\FormInput::CategoryRepeater();
                            }
                            if($type == 2){
                                $field[] = \App\Helper\FormInput::SubCategoryRepeater();
                            }
                            if($type == 3){
                                $field[] = \App\Helper\FormInput::tags();
                            }
                            if($type == 4){
                                $field[] = \App\Helper\FormInput::BrandRepeater();
                            }
                            if($type == 5){
                                $field[] = \App\Helper\FormInput::ProductRepeater();
                            }
                            if($type == 6){
                                $field[] = \App\Helper\FormInput::Url();
                            }
                            return $field;
                        }),
                ])
                    ->columnSpan(['lg' => 4]),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('OrderStatus')->schema([
                        Toggle::make('status')
                            ->label('OrderStatus')
                            ->inline(false)
                            ->onIcon('tabler-eye')
                            ->offIcon('tabler-eye-off'),
                        Toggle::make('closable')
                            ->label('Closable')
                            ->inline(false)
                            ->onIcon('tabler-check')
                            ->offIcon('tabler-x'),
                    ]),
                    Forms\Components\Section::make('Image')->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->hiddenLabel()
                            ->maxSize(100)
                            ->disk('public')
                            ->image()
                            ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file): string {
                                    $image_info = FileHelper::uploadToLocalFilament($file, 'banner');
                                    return $image_info['name'];
                                }),
                    ])
                ])->columnSpan(['lg' => 1]),
                Forms\Components\Hidden::make('admin_id')
                    ->dehydrateStateUsing(fn() => (new \App\Helper\Setting)->admin_id())
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image')
                    ->formatStateUsing(fn ($state):HtmlString => new HtmlString('<img src='.asset('uploads/thumb-'.$state).' alt="category-image" height="80" width="80" lazy="loading"/>')),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return Setting::banner($state);
                    }),
                Tables\Columns\ToggleColumn::make('status')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),
                Tables\Columns\ToggleColumn::make('closable')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
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
            'index' => \App\Filament\Admin\Resources\Ui\BannerResource\Pages\ListBanners::route('/'),
            'create' => \App\Filament\Admin\Resources\Ui\BannerResource\Pages\CreateBanner::route('/create'),
            'edit' => \App\Filament\Admin\Resources\Ui\BannerResource\Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
