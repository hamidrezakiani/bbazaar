<?php

namespace App\Filament\Admin\Resources\Ui;

use App\Filament\Admin\Resources\HeaderLinkResource\Pages;
use App\Filament\Admin\Resources\HeaderLinkResource\RelationManagers;
use App\Filament\Admin\Resources\Ui;
use App\Models\HeaderLink;
use App\Models\HeaderLinkLang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HeaderLinkResource extends Resource
{
    protected static ?string $model = HeaderLink::class;
    protected static ?string $navigationGroup = 'UI';
    protected static ?string $navigationIcon = 'tabler-link';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Header Link Form')->schema([
                    Forms\Components\Select::make('type')
                        ->options([
                            1 => 'Left',
                            2 => 'Right'
                        ])->required()
                        ->default(1),
                    Forms\Components\TextInput::make('title')
                        ->dehydrateStateUsing(fn($state) => Str::upper($state))
                        ->required(),
                    Forms\Components\TextInput::make('url')
                        ->required(),
                    Forms\Components\Hidden::make('admin_id')
                        ->dehydrateStateUsing(fn() => Auth::user()->id)
                ])->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SelectColumn::make('type')
                    ->options([
                        1 => 'Left',
                        2 => 'Right'
                    ]),
                Tables\Columns\TextInputColumn::make('title'),
                Tables\Columns\TextInputColumn::make('url'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()
                    ->action(function ($record) {
                        try {
                            HeaderLinkLang::where('header_link_id', $record->id)->delete();
                            if ($record->delete()) {
                                Notification::make()
                                    ->title('Success')
                                    ->body("Deleted Successfully")
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $ex) {
                            Notification::make()
                                ->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            Ui\HeaderLinkResource\RelationManagers\LanguageRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Ui\HeaderLinkResource\Pages\ListHeaderLinks::route('/'),
            'create' => Ui\HeaderLinkResource\Pages\CreateHeaderLink::route('/create'),
            'edit' => Ui\HeaderLinkResource\Pages\EditHeaderLink::route('/{record}/edit'),
        ];
    }
}
