<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\RatingReviewResource\Pages;
use App\Filament\Admin\Resources\Admin\RatingReviewResource\RelationManagers;
use App\Helper\Setting;
use App\Models\Helper\FileHelper;
use App\Models\Product;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RatingReviewResource extends Resource
{
    protected static ?string $model = RatingReview::class;
    protected static ?string $navigationIcon = 'tabler-stars';
    protected static ?string $navigationGroup = 'Product';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('rating')->sortable(),
                Tables\Columns\TextColumn::make('review')
                    ->tooltip(fn ($state) => $state)
                    ->words(10),
                Tables\Columns\ImageColumn::make('review_images.image')
                    ->disk('public')
                    ->stacked(),
                Tables\Columns\TextColumn::make('product.title')
                    ->sortable()
                    ->tooltip(fn ($state) => $state)
                    ->words(10),
                Tables\Columns\TextColumn::make('created_at')->since()
                ->tooltip(fn ($state) => $state),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::CustomDelete()
                    ->action(function ($record) {
                        try {

                                $ratingReview = RatingReview::with('product_admin')->find($record->id);
                                $reviewImages = ReviewImage::where('rating_review_id', $record->id)->get();

                                foreach ($reviewImages as $i) {
                                    ReviewImage::where('id', $i->id)->delete();
                                    FileHelper::deleteFile($i->image);
                                }

                                if ($ratingReview->where('id', $record->id)->delete()) {

                                    $product = Product::find($ratingReview->product_id);
                                    $total = $product->rating * $product->review_count;


                                    if($product->review_count > 1){
                                        $avg = 0;
                                    } else {
                                        $avg = ($total - $ratingReview->rating) / $product->review_count - 1;
                                    }

                                    Product::where('id', $ratingReview->product_id)
                                        ->update([
                                            'rating' => $avg,
                                            'review_count' => $product->review_count - 1
                                        ]);
                                }

                            Notification::make()
                                ->title('Deleted')
                                ->body('Review Rating Successfully Deleted.')
                                ->success()
                                ->send();

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\RatingReviewResource\Pages\ListRatingReviews::route('/'),
            //            'create' => Pages\CreateRatingReview::route('/create'),
            //            'edit' => Pages\EditRatingReview::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
