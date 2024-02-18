<?php

namespace App\Filament\Seller\Resources\RatingReviewResource\Pages;

use App\Filament\Seller\Resources\RatingReviewResource;
use App\Models\RatingReview;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRatingReviews extends ListRecords
{
    protected static string $resource = RatingReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTableQuery(): ?Builder
    {
        $query = RatingReview::join('products as p', function ($join) {
            $join->on('p.id', '=', 'rating_reviews.product_id');
            $join->where('p.admin_id', auth()->user()->id);
        });
         $query->where('admin_id', auth()->user()->id);
        return $query;
    }
}
