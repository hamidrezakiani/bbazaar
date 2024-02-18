<?php

namespace App\Filament\Admin\Resources\RatingReviewResource\Pages;

use App\Filament\Admin\Resources\RatingReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListRatingReviews extends ListRecords
{
    protected static string $resource = RatingReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
