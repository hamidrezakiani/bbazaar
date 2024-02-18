<?php

namespace App\Filament\Admin\Resources\Ui;

use App\Filament\Admin\Resources\Ui\HomeSliderResource\RelationManagers\LanguageRelationManager;
use App\Models\Helper\FileHelper;
use App\Models\HomeSliderLang;
use App\Models\HomeSliderSourceBrand;
use App\Models\HomeSliderSourceCategory;
use App\Models\HomeSliderSourceProduct;
use App\Models\HomeSliderSourceSubCategory;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use App\Models\Brand;
use App\Helper\Select;
use App\Helper\Setting;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms\Form;
use App\Models\HomeSlider;
use Filament\Tables\Table;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Wallo\FilamentSelectify\Components\ToggleButton;
use App\Filament\Admin\Resources\Admin\Ui\HomeSliderResource\Pages;
use App\Filament\Admin\Resources\Admin\Ui\HomeSliderResource\RelationManagers;

class HomeSliderResource extends Resource
{
    protected static ?string $model = HomeSlider::class;
    protected static ?string $navigationGroup = 'UI';
    protected static ?string $navigationIcon = 'tabler-slideshow';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Slider Form')->schema([
                    Forms\Components\TextInput::make('title')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->required(),
                    Forms\Components\TextInput::make('slug')
                        ->dehydrated()
                        ->required()
                        ->unique(HomeSlider::class, 'slug', ignoreRecord: true),

                    Forms\Components\Select::make('type')
                        ->required()
                        ->reactive()
                        ->options(Setting::sliderType())
                        ->afterStateUpdated(function ($state) {
                            if ($state != 1) {
                                $checkType = HomeSlider::where('type', $state)->first();
                                if ($checkType != null) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Slider Type already Exist')
                                        ->danger()
                                        ->send();
                                } else {
                                    return Setting::sliderType();
                                }
                            }
                            return Setting::sliderType();
                        }),

                    Forms\Components\Select::make('source_type')
                        ->required()
                        ->reactive()
                        ->options(Setting::sourceType()),

                    Forms\Components\Group::make()
                        ->schema(function (Forms\Get $get) {
                            $type = $get('source_type');
                            $field = [];
                            if ($type == 1) {
                                $field[] = \App\Helper\FormInput::CategoryRepeater();
                            }
                            if ($type == 2) {
                                $field[] = \App\Helper\FormInput::SubCategoryRepeater();
                            }
                            if ($type == 3) {
                                $field[] = \App\Helper\FormInput::tags();
                            }
                            if ($type == 4) {
                                $field[] = \App\Helper\FormInput::BrandRepeater();
                            }
                            if ($type == 5) {
                                $field[] = \App\Helper\FormInput::ProductRepeater();
                            }
                            if ($type == 6) {
                                $field[] = \App\Helper\FormInput::Url();
                            }
                            return $field;
                        }),

                    Forms\Components\Hidden::make('admin_id')
                        ->dehydrateStateUsing(fn() => (new \App\Helper\Setting)->admin_id())
                ])->columnSpan(['lg' => 3]),
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('OrderStatus')->schema([
                        Toggle::make('status')
                            ->label('OrderStatus')
                            ->inline(false)
                            ->onIcon('tabler-eye')
                            ->offIcon('tabler-eye-off'),
                    ]),

                    Forms\Components\Section::make('Image')->schema([
                        FileUpload::make('image')
                            ->label('Image')
                            ->hiddenLabel()
                            ->disk('public')
                            ->image()
                            ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file): string {
                                    $image_info = FileHelper::uploadToLocalFilament($file, 'home-slider');
                                    return $image_info['name'];
                                }),
                    ])
                ])->columnSpan(['lg' => 2]),
            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                ->hidden(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            1 => 'Main',
                            2 => 'Right Top',
                            3 => 'Right Bottom'
                        };
                    }),
                Tables\Columns\TextColumn::make('source_type')
                    ->formatStateUsing(function ($record, $state) {
                        if ($state === 1) {
                            foreach ($record->source_categories as $category) {
                                if ($category !== null && isset($category->category->title)) {
                                    return Setting::sliderSourceType($state) . ': ' . $category->category->title;
                                }
                            }
                        }
                        if ($state == 2) {
                            foreach ($record->source_sub_categories as $subCategory) {
                                if ($subCategory !== null && isset($subCategory->sub_category->title)) {
                                    return Setting::sliderSourceType($state) . ': ' . $subCategory->sub_category->title;
                                }
                            }
                        }
                        if ($state == 3) {
                            return $record->tags;
                        }
                        if ($state == 4) {
                            foreach ($record->source_brands as $brand) {
                                if ($brand !== null && isset($brand->brand->title)) {
                                    return Setting::sliderSourceType($state) . ': ' . $brand->brand->title;
                                }
                            }
                        }
                        if ($state == 5) {
                            foreach ($record->source_brands as $brand) {
                                if ($brand !== null && isset($brand->brand->title)) {
                                    return Setting::sliderSourceType($state) . ': ' . $brand->brand->title;
                                }
                            }
                        }
                        if ($state == 6) {
                            return $record->url;
                        }
                        return Setting::sliderSourceType($state);
                    })->badge(),
                Tables\Columns\ToggleColumn::make('status')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()
                    ->disabled(fn ($record):bool => $record->type !== 1)
                    ->action(function ($record): void {
                        try {
                            if($record->type === 1) {
                                HomeSliderSourceBrand::where('home_slider_id', $record->id)->delete();
                                HomeSliderSourceCategory::where('home_slider_id', $record->id)->delete();
                                HomeSliderSourceSubCategory::where('home_slider_id', $record->id)->delete();
                                HomeSliderSourceProduct::where('home_slider_id', $record->id)->delete();
                                HomeSliderLang::where('home_slider_id', $record->id)->delete();
                                if ($record->delete()) {
                                    Notification::make()
                                        ->title('Success')
                                        ->body('Home Slider Deleted Successfully')
                                        ->success()
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->title('Error')
                                    ->body('You cannot Delete this record')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $ex) {
                            Notification::make()
                                ->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index' => \App\Filament\Admin\Resources\Ui\HomeSliderResource\Pages\ListHomeSliders::route('/'),
            'create' => \App\Filament\Admin\Resources\Ui\HomeSliderResource\Pages\CreateHomeSlider::route('/create'),
            'edit' => \App\Filament\Admin\Resources\Ui\HomeSliderResource\Pages\EditHomeSlider::route('/{record}/edit'),
        ];
    }
}
