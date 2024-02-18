<?php

namespace App\Filament\Admin\Resources\Ui\HomeSliderResource\Pages;

use App\Filament\Admin\Resources\Ui\HomeSliderResource;
use App\Models\HomeSlider;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeSlider extends CreateRecord
{
    protected static string $resource = HomeSliderResource::class;

    protected function beforeCreate(): void
    {
        $checkType = HomeSlider::where('type', $this->form->getState()['type'])->first();
        if ($checkType->type != 1) {
            Notification::make()
                ->title('Error')
                ->body('Slider Type already Exist')
                ->danger()
                ->send();
            $this->halt();
        }
    }
}
