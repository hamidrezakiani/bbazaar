<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Livewire\WithPagination;
use SplFileInfo;
use Illuminate\Pagination\LengthAwarePaginator;

class Files extends Page
{
    use WithPagination;
    protected static ?string $navigationIcon = 'tabler-photo';
    protected static ?string $navigationGroup = "Setting";
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.admin.pages.files';
    protected static ?string $title = 'Images';

    protected collection $data;
    public array $prefixes = [];
    protected $paginate = [];

    public function __construct()
    {
        $this->data = collect();
    }
    public function mount(): void
    {
        $this->refreshData();
    }

    public function updatedSearchTerm(): void
    {
        $this->refreshData();
    }

    private function refreshData(): void
    {
        $directory = public_path('uploads');
        $files = File::files($directory);
        $this->prefixes = [
            'banner',
            'brand',
            'category',
            'email_logo',
            'footer',
            'header_logo',
            'product',
            'slider',
            'sub-category',
            'thumb',
            'favicon',
            'review',
            'logo'
        ];

        // $this->data = collect($files)->reject(function ($file) {
        //     foreach ($this->prefixes as $prefix) {
        //         if (str_starts_with($file->getFilename(), $prefix)) {
        //             return true;
        //         }
        //     }
        //     return false;
        // });
        
        
        
        
        $this->data = collect($files)->reject(function ($file) {
            foreach ($this->prefixes as $prefix) {
                if (str_starts_with($file->getFilename(), $prefix)) {
                    return true;
                }
            }
            return false;
        });

        $perPage = 100; // Number of items per page
        $page = request()->get('page', 1); // Get the current page from the request

        $total = $this->data->count();
        $lastPage = ceil($total / $perPage);

        if ($page < 1 || $page > $lastPage) {
            $page = 1;
        }

        $paginatedData = new LengthAwarePaginator(
            $this->data->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $this->paginate = $paginatedData;
        
        
    }

    public function delete($name):void{

        if ($name) {
            $filePath = public_path('uploads/'.$name);
            if (File::exists($filePath)) {
                File::delete($filePath);
                $this->refreshData();
                Filament\Notifications\Notification::make()
                    ->title('This Deleted Successfully')
                    ->inline()
                    ->success()
                    ->send();
            }
        } else {
            Filament\Notifications\Notification::make()
                ->title('Error, Something went wrong.')
                ->inline()
                ->danger()
                ->send();
        }
    }
}
