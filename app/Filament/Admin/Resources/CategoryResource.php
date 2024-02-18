<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Filament\Admin\Resources\CategoryResource\RelationManagers;
use App\Filament\Admin\Resources\CategoryResource\RelationManagers\CategoryLangRelationManager;
use App\Helper\Setting;
use App\Models\BannerSourceCategory;
use App\Models\Category;
use App\Models\CategoryLang;
use App\Models\Helper\FileHelper;
use App\Models\HomeSliderSourceCategory;
use App\Models\Product;
use App\Models\ProductCategory;
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
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Wallo\FilamentSelectify\Components\ButtonGroup;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'tabler-category';
    protected static ?string $navigationGroup = 'Product';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Category Form')
                    ->schema([
//                        FormInput\Components\Select::make('parent')
//                            ->searchable()
//                            ->preload()
//                            ->relationship('category', 'title'),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->lazy()
                            ->afterStateUpdated(fn(Set $set, $state) => $set('slug', $state))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug'),
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
//                                                        $filename = (string)'product-' . time() . '.' . $file->guessExtension();
                                    $image_info = FileHelper::uploadToLocalFilament($file, 'category');
                                    return $image_info['name'];
                                }),
                    ])
                    ->columnSpan(['lg' => 3]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status')
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
                                // Toggle::make('in_footer')
                                //     ->label('In Footer')
                                //     ->inline(false)
                                //     ->onIcon('tabler-eye')
                                //     ->offIcon('tabler-eye-off'),
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
                Tables\Columns\TextColumn::make('parent_data.title')
                    ->label('Parent'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status')
                    ->inline(false)
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off')
                    ->visible(fn() => Auth::user()->hasRole('super_admin') == 'super_admin'),
                // Tables\Columns\ToggleColumn::make('featured')
                //     ->label('Featured')
                //     ->inline(false)
                //     ->onIcon('tabler-check')
                //     ->offIcon('tabler-x')
                //     ->visible(fn() => Auth::user()->hasRole('super_admin') == 'super_admin'),
                // Tables\Columns\ToggleColumn::make('in_footer')
                //     ->label('In Footer')
                //     ->inline(false)
                //     ->onIcon('tabler-check')
                //     ->offIcon('tabler-x')
                //     ->visible(fn() => Auth::user()->hasRole('super_admin') == 'super_admin'),
                Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(fn($record) => $record),
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {
                        $cateId = $record->id;
                        $product = Product::where('category_id', $record->id)->first();
                        if ($product) {
                            Notification::make()
                                ->title('Unable to Delete')
                                ->body("This {$record->title} record used by Product.")
                                ->danger()
                                ->send();

                        } else {

                            HomeSliderSourceCategory::where('category_id', $record->id)->delete();
                            BannerSourceCategory::where('category_id', $record->id)->delete();
                            ProductCategory::where('category_id', $record->id)->delete();
                            CategoryLang::where('category_id', $record->id)->delete();

                            if ($record->delete()) {
                                Category::where('parent', $cateId)->update(['parent' => 0]);
                                Notification::make()
                                    ->title('Success')
                                    ->body("Deleted Successfully")
                                    ->success()
                                    ->send();
                            }
                        }
                    } catch (\Exception $ex) {
                        Notification::make()
                            ->title('Error')
                            ->body($ex->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            CategoryLangRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CategoryResource\Pages\ListCategories::route('/'),
            'create' => \App\Filament\Admin\Resources\CategoryResource\Pages\CreateCategory::route('/create'),
            'edit' => \App\Filament\Admin\Resources\CategoryResource\Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
