<?php

namespace App\Filament\Admin\Pages\Ui;

use App\Helper\Setting;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Page as Pages;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class FooterLink extends Page implements HasTable
{
    use HasPageShield, InteractsWithTable;
    protected static ?string $navigationIcon = 'tabler-link';
    protected static ?string $navigationGroup = 'UI';
    protected static string $view = 'filament.admin.pages.ui.footer-link';
    protected static ?int $navigationSort = 3;


    public static function table(Table $table): Table
    {
        return $table
            ->query(\App\Models\FooterLink::query())
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => Setting::footerLinkType($state)),
                SelectColumn::make('page_id')
                    ->label('Page')
                    ->options(Pages::all()->pluck('title', 'id'))
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->form(self::getFooterLinkForm())
                    ->modalWidth('lg')
                    ->label('New Link')
                    ->icon('tabler-circle-plus')
                    ->createAnother(false),
            ])
            ->actions([
                \App\Helper\Actions::EditAction()
                    ->form(self::getFooterLinkForm())
                    ->modalWidth('lg'),
                \App\Helper\Actions::DeleteAction(),
            ])->heading('Footer Links')
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getFooterLinkForm():array{
        return [
            Select::make('type')
                ->options([
                    1 => 'SERVICE',
                    2 => 'ABOUT'
                ])->required()->searchable(),
            Select::make('page_id')->label('Page')
                ->required()
                ->searchable()
                ->options(Pages::all()->pluck('title', 'id')),
            Hidden::make('admin_id')->dehydrateStateUsing(fn () => Auth::user()->id)
        ];
    }
}
