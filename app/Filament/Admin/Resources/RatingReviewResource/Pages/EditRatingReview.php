<?php

namespace App\Filament\Admin\Resources\RatingReviewResource\Pages;

use App\Filament\Admin\Resources\RatingReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRatingReview extends EditRecord
{
    protected static string $resource = RatingReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
