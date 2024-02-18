<?php

namespace App\Livewire\Admin\Setting;

use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Artisan;

class ClearCache extends Component
{

    public function clear(): void
    {
        Artisan::call('optimize:clear');

        Notification::make()
            ->title('Updated successfully!!!')
            ->success()
            ->send();
    }

    public function render(): View
    {
        return view('livewire.admin.setting.clear-cache');
    }
}