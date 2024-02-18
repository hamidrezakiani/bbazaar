<?php

namespace App\Livewire\Admin\Ui;

use App\Models\Helper\FileHelper;
use App\Models\Page;
use App\Helper\Setting;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Wallo\FilamentSelectify\Components\ToggleButton;

class PaymentSocialLink extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public static function table(Table $table): Table
    {
        return $table
            ->query(\App\Models\FooterImageLink::query())
            ->columns([
                TextColumn::make('image')
                    ->formatStateUsing(fn ($state):HtmlString => new HtmlString('<img src='.asset('uploads/thumb-'.$state).' alt="social-image" height="80" width="80" lazy="loading"/>')),
                TextColumn::make('type')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn($state) => Setting::footerImageLinkType($state)),
                TextColumn::make('title'),
                TextColumn::make('link'),
                ToggleColumn::make('status')
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->form(self::socialLinkForm())->modalWidth('lg')
                    ->label('New Link')
                    ->icon('tabler-circle-plus')
                    ->createAnother(false),
            ])
            ->actions([
                \App\Helper\Actions::EditAction()->form(self::socialLinkForm())->modalWidth('lg'),
                \App\Helper\Actions::DeleteAction(),
            ])->heading('Payment & Social Links')
            ->paginationPageOptions(Setting::pagination());
    }


    public function render()
    {
        return view('livewire.admin.ui.payment-social-link');
    }

    public static function socialLinkForm(): array
    {
        return [
            FileUpload::make('image')
            ->label('Image')
            ->hiddenLabel()
            ->maxSize(100)
            ->disk('public')
            ->image()
            ->mutateDehydratedStateUsing(fn($state): string => $state == null ? 'default-image.webp' : $state)
            ->getUploadedFileNameForStorageUsing(
                function (TemporaryUploadedFile $file): string {
                    $image_info = FileHelper::uploadToLocalFilament($file, 'footer');
                    return $image_info['name'];
                }),
            Select::make('type')
                ->options([
                    1 => 'PAYMENT',
                    2 => 'SOCIAL'
                ])->required()->searchable(),
            TextInput::make('title')
                ->required(),
            TextInput::make('link')
                ->url()
                ->required(),
            Toggle::make('status')
                ->offIcon('tabler-eye-off')
                ->onIcon('tabler-eye')
                ->default(true),
            Hidden::make('admin_id')->dehydrateStateUsing(fn() => Auth::user()->id)
        ];
    }
}
