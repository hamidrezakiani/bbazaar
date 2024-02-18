<?php

namespace App\Filament\Admin\Resources\Ui\HeaderLinkResource\RelationManagers;

use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

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
            ]);
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
                \App\Helper\Actions::addLanguageAction(),
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
