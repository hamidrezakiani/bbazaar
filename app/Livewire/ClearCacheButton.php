<?php

namespace App\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class ClearCacheButton extends Component
{
    public function clearCache(): void
    {
        Artisan::call('optimize:clear');
        Notification::make()
            ->title('System Cache Cleared...')
            ->success()
            ->send()
            ->inline();
    }

    public function render()
    {
        return view('livewire.clear-cache-button');
    }
}
