<?php

namespace App\Filament\Admin\Resources;

use Exception;
use App\Filament\Admin\Resources\SubCategoryResource\Pages;
use App\Filament\Admin\Resources\SubCategoryResource\RelationManagers;
use App\Filament\Admin\Resources\SubCategoryResource\RelationManagers\SubCategoryLangRelationManager;
use App\Helper\Setting;
use App\Models\BannerSourceSubCategory;
use App\Models\Helper\FileHelper;
use App\Models\HomeSliderSourceSubCategory;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\SubCategoryLang;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Wallo\FilamentSelectify\Components\ButtonGroup;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;
    protected static ?string $navigationIcon = 'tabler-category-2';
    protected static ?string $navigationGroup = 'Product';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sub Category Form')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->lazy()
                            ->afterStateUpdated(fn(Set $set, $state) => $set('slug', Str::slug($state)))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug'),
                        Forms\Components\Select::make('category_id')
                            ->relationship(name: 'categ', titleAttribute: 'title')
                            ->label('Category')
                            ->searchable(),
                        Forms\Components\TextInput::make('meta_title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        FileUpload::make('image')
                            ->label('Image')
                            ->hiddenLabel()
                            ->disk('public')
                            ->image()
                            ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file): string {
                                    $image_info = FileHelper::uploadToLocalFilament($file, 'sub-category');
                                    return $image_info['name'];
                                }),
                    ])->columnSpan(['lg' => 3]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('OrderStatus')
                            ->schema([
                                Toggle::make('featured')
                                    ->label('Featured')
                                    ->inline(false)
                                    ->onIcon('tabler-eye')
                                    ->offIcon('tabler-eye-off'),
                                Toggle::make('status')
                                    ->inline(false)
                                    ->onIcon('tabler-eye')
                                    ->offIcon('tabler-eye-off'),
                            ])
                    ])->columnSpan(['lg' => 2]),
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.title'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('OrderStatus')
                    ->inline(false)
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),

                Tables\Columns\ToggleColumn::make('featured')
                    ->label('Featured')
                    ->inline(false)
                    ->onIcon('tabler-check')
                    ->offIcon('tabler-x'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {
                        DB::transaction(function () use ($record) {
                            $subCategory = SubCategory::find($record->id);
                            $product = Product::where('subcategory_id', $record->id)->exists();
                            $homeSlidersSourceSubCategory = HomeSliderSourceSubCategory::where('sub_category_id', $record->id)->exists();
                            $bannerSourceSubCat = BannerSourceSubCategory::where('sub_category_id', $record->id)->exists();

                            if ($product) {
                                throw new Exception('Sub Category is used by a product.');
                            }

                            if ($homeSlidersSourceSubCategory) {
                                throw new Exception('Sub Category is used by a Home Slider.');
                            }

                            if ($bannerSourceSubCat) {
                                throw new Exception('Sub Category is used by a Banner.');
                            }

                            SubCategoryLang::where('sub_category_id', $record->id)->delete();
                            $subCategory->delete();
                            FileHelper::deleteFile($subCategory->image);

                            Notification::make()
                                ->title('Success')
                                ->body('Sub Category Successfully Deleted.')
                                ->success()
                                ->send();
                        });
                    } catch (Exception $ex) {
                        Notification::make()
                            ->title('Error')
                            ->body($ex->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                })
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            SubCategoryLangRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubCategories::route('/'),
            'create' => Pages\CreateSubCategory::route('/create'),
            'edit' => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
