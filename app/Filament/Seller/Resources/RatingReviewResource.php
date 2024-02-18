<?php

namespace App\Filament\Seller\Resources;

use Filament\Tables;
use App\Helper\Setting;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ReviewImage;
use App\Models\RatingReview;
use Filament\Resources\Resource;
use App\Models\Helper\FileHelper;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Filament\Seller\Resources\RatingReviewResource\Pages;
use App\Filament\Seller\Resources\RatingReviewResource\RelationManagers;

class RatingReviewResource extends Resource
{
    protected static ?string $model = RatingReview::class;
    protected static ?string $navigationIcon = 'tabler-stars';
    protected static ?string $navigationGroup = 'Product';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
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
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Seller\Resources\RatingReviewResource\Pages\ListRatingReviews::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        if(Auth::check()) {
            $query = RatingReview::join('products as p', function ($join) {
                $join->on('p.id', '=', 'rating_reviews.product_id');
                $join->where('p.admin_id', auth()->user()->id);
            });
            $query->where('admin_id', auth()->user()->id);
            return $query->count();
        }
        return 0;
    }
}
