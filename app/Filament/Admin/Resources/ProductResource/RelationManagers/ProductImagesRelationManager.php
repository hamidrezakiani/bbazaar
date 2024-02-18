<?php

namespace App\Filament\Admin\Resources\ProductResource\RelationManagers;

use App\Models\Helper\FileHelper;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'product_images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->label('Images')
                    ->hiddenLabel()
                    ->disk('public')
                    ->image()
                    ->required()
                    ->getUploadedFileNameForStorageUsing(
                        function (TemporaryUploadedFile $file): string {
                            $image_info = FileHelper::uploadToLocalFilament($file, 'product');
                            return $image_info['name'];
                        }),
                Forms\Components\Hidden::make('admin_id')
                    ->dehydrateStateUsing(fn() => Auth::user()->id)
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image')
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Upload Image'),
            ])
            ->actions([
                \App\Helper\Actions::DeleteAction(),
            ]);
    }
}
