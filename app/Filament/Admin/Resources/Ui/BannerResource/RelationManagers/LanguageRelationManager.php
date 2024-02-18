<?php

namespace App\Filament\Admin\Resources\Ui\BannerResource\RelationManagers;

use App\Models\Language;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class LanguageRelationManager extends RelationManager
{
    protected static string $relationship = 'Language';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('lang')
                    ->options(Language::all()->pluck('name', 'code'))
                    ->searchable()
                    ->required()
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('lang'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()
                    ->createAnother(false),
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::DeleteAction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
