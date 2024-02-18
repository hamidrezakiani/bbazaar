<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class VideoObserver
{
    public function deleted(Model $model): void
    {
        if ($model->video) {
            $filePath = public_path('uploads/'.$model->video);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        if ($model->video_thumb) {
            $filePath = public_path('uploads/'.$model->video_thumb);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
    }

    public function updating(Model $model): void
    {
        if ($model->isDirty('video')) {
            $originalImagePath = $model->getOriginal('video');
            if ($originalImagePath) {
                $filePath = public_path('uploads/'.$originalImagePath);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
        }

        if ($model->isDirty('video_thumb')) {
            $originalImagePath = $model->getOriginal('video_thumb');
            if ($originalImagePath) {
                $filePath = public_path('uploads/'.$originalImagePath);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }
        }
    }
}
