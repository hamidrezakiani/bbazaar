<?php

namespace App\Livewire\Admin\Setting;

use Exception;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Symfony\Component\Dotenv\Dotenv;

class MediaStorage extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'mediaStorage' => env('MEDIA_STORAGE'),
            'thumbPrefix' => env('THUMB_PREFIX'),
            'defaultImage' => env('DEFAULT_IMAGE'),
            'cdnUrl' => env('CDN_URL'),
            'gcProjectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'gcStorageBucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
            'gcStoragePathPrefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX')
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('mediaStorage')->label('Media Storage')
                ->reactive()
                ->options([
                    'LOCAL' => 'LOCAL',
                    'GCS' => 'Google Cloud Storage',
                    'URL' => 'URL'
                ]),
            TextInput::make('thumbPrefix')->label('Thumb Prefix'),
            TextInput::make('defaultImage')->label('Default image'),
            TextInput::make('cdnUrl')->label('CDN Url')
                ->live()
                ->hidden(fn (Get $get) => $get('mediaStorage') == 'LOCAL'),
            Group::make()->schema([
                TextInput::make('gcProjectId')->label('Google Cloud Project ID'),
                TextInput::make('gcStorageBucket')->label('Google Cloud Storage Bucket'),
                TextInput::make('gcStoragePathPrefix')->label('Google Cloud Storage Path Prefix'),
            ])->hidden(function (Get $get) {
                if ($get('mediaStorage') == 'LOCAL' || $get('mediaStorage') == 'URL') return true;
                return false;
            }),
        ])->statePath('data');
    }

    public function submit(): void
    {
//        try {
//            $db = [
//                "MEDIA_STORAGE" => $this->data['mediaStorage'],
//                "THUMB_PREFIX" => $this->data['thumbPrefix'],
//                "DEFAULT_IMAGE" => $this->data['defaultImage']
//            ];
//
//            if ($this->data['mediaStorage'] == config('env.media.URL')) {
//                $db["CDN_URL"] = $this->data['cdnUrl'];
//            } else if ($this->data['mediaStorage'] == config('env.media.GCS')) {
//                $db["CDN_URL"] = $this->data['cdnUrl'];;
//                $db["GOOGLE_CLOUD_PROJECT_ID"] = $this->data['gcProjectId'];
//                $db["GOOGLE_CLOUD_STORAGE_BUCKET"] = $this->data['gcStorageBucket'];
//                $db["GOOGLE_CLOUD_STORAGE_PATH_PREFIX"] = $this->data['gcStoragePathPrefix'];
//            }
//
//            $path = base_path('env.php');
//            if (file_exists($path)) {
//                Artisan::call('optimize:clear');
////                Artisan::call('config:clear', ['--force', true]);
////                Artisan::call('route:clear', ['--force', true]);
////                Artisan::call('cache:clear', ['--force', true]);
////                Artisan::call('view:clear', ['--force', true]);
//                foreach ($db as $key => $value) {
//                    file_put_contents($path, str_replace(
//                        $key . '=' . env($key),
//                        $key . '=' . $value,
//                        file_get_contents($path)
//                    ));
//                }
//
//                Notification::make()
//                    ->title('Successfully')
//                    ->success()
//                    ->body('Media Storage Setting updated successfully')
//                    ->send();
//            }
//        } catch (Exception $ex) {
//            Notification::make()
//                ->title('Error')
//                ->success()
//                ->body($ex->getMessage())
//                ->send();
//        }
        try {
            $db = [
                "MEDIA_STORAGE" => $this->data['mediaStorage'],
                "THUMB_PREFIX" => $this->data['thumbPrefix'],
                "DEFAULT_IMAGE" => $this->data['defaultImage']
            ];

            if ($this->data['mediaStorage'] == config('env.media.URL')) {
                $db["CDN_URL"] = $this->data['cdnUrl'];
            } else if ($this->data['mediaStorage'] == config('env.media.GCS')) {
                $db["CDN_URL"] = $this->data['cdnUrl'];
                $db["GOOGLE_CLOUD_PROJECT_ID"] = $this->data['gcProjectId'];
                $db["GOOGLE_CLOUD_STORAGE_BUCKET"] = $this->data['gcStorageBucket'];
                $db["GOOGLE_CLOUD_STORAGE_PATH_PREFIX"] = $this->data['gcStoragePathPrefix'];
            }

            $envPath = base_path('.env');
            $envContents = file_get_contents($envPath);

            foreach ($db as $key => $value) {
                $envContents = preg_replace('/^' . $key . '=.*/m', $key . '=' . $value, $envContents);
            }

            file_put_contents($envPath, $envContents);
            Artisan::call('optimize:clear');

            Notification::make()
                ->title('Successfully')
                ->success()
                ->body('Media Storage Setting updated successfully')
                ->send();
        } catch (Exception $ex) {
            Notification::make()
                ->title('Error')
                ->success()
                ->body($ex->getMessage())
                ->send();
        }
    }

    public function render(): View
    {
        return view('livewire.admin.setting.media-storage');
    }
}
