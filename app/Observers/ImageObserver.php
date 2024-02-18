<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ImageObserver
{
    public function deleted(Model $model): void
    {
        if ($model->image) {
            $filePath = public_path('uploads/'.$model->image);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    public function updating(Model $model): void
    {
        if ($model->isDirty('image')) {
            $originalImagePath = $model->getOriginal('image');
            if ($originalImagePath) {
                $filePath = public_path('uploads/'.$originalImagePath);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
        }
    }
}
