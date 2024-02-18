<?php

namespace App\Filament\Admin\Resources;

use App\Models\BannerSourceBrand;
use App\Models\BrandLang;
use App\Models\Helper\FileHelper;
use App\Models\HomeSliderSourceBrand;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables;
use App\Models\Brand;
use App\Helper\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Wallo\FilamentSelectify\Components\ToggleButton;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Filament\Admin\Resources\Admin\BrandResource\Pages;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Admin\Resources\Admin\BrandResource\RelationManagers;
use App\Filament\Admin\Resources\BrandResource\RelationManagers\LanguageRelationManager;
use Filament\Forms\Components\Grid;
use Wallo\FilamentSelectify\Components\ButtonGroup;

class BrandResource extends Resource
{

    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'tabler-tag';

    protected static ?string $navigationGroup = 'Product';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Brand Form')->schema([
                    Forms\Components\TextInput::make('title')
                        ->lazy()
                        ->afterStateUpdated(fn(Forms\Set $set, $state) => $set('slug', Str::slug($state)))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')->required(),

                    FileUpload::make('image')
                        ->label('Image')
                        ->disk('public')
                         ->maxSize(150)
                        ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                        ->getUploadedFileNameForStorageUsing(
                            function (TemporaryUploadedFile $file): string {
                                $image_info = FileHelper::uploadToLocalFilament($file, 'brand');
                                return $image_info['name'];
                            }),
                ])->columnSpan(['lg' => 4]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('OrderStatus')->schema([
                            Toggle::make('status')
                                ->inline(false)
                                ->onIcon('tabler-eye')
                                ->offIcon('tabler-eye-off'),

                            Toggle::make('featured')
                                ->label('Featured')
                                ->inline(false)
                                ->onIcon('tabler-check')
                                ->offIcon('tabler-x'),
                        ]),
                    ])->columnSpan(['lg' => 1]),
                Forms\Components\Hidden::make('admin_id')
                    ->dehydrateStateUsing(fn() => Auth::user()->id)
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image')
                    ->formatStateUsing(fn ($state):HtmlString => new HtmlString('<img src='.asset('uploads/thumb-'.$state).' alt="category-image" style="height: 2.5rem;"/>')),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off')
                    ->visible(fn () => Auth::user()->hasRole('super_admin') == 'super_admin'),

                Tables\Columns\ToggleColumn::make('featured')
                    ->label('Featured')
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x')
                    ->visible(fn () => Auth::user()->hasRole('super_admin') == 'super_admin'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(fn($record) => $record),
                \App\Helper\Actions::DeleteAction(fn($record) => $record)
                    ->before(function ($record) {
                        try {
                            $hasProduct = Product::where('brand_id', $record->id)->first();
                            if ($hasProduct) {
                                Product::where('brand_id', $record->id)->update(['brand_id', $record->id]);
                            }
                            HomeSliderSourceBrand::where('brand_id', $record->id)->delete();
                            BannerSourceBrand::where('brand_id', $record->id)->delete();
                            BrandLang::where('brand_id', $record->id)->delete();

                        } catch (\Exception $ex) {
                            Notification::make()
                                ->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            LanguageRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\BrandResource\Pages\ListBrands::route('/'),
            'create' => \App\Filament\Admin\Resources\BrandResource\Pages\CreateBrand::route('/create'),
            'edit' => \App\Filament\Admin\Resources\BrandResource\Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
