<?php

namespace App\Filament\Admin\Resources\CollectionResource\RelationManagers;

use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class CollectionLanguageRelationManager extends RelationManager
{
    protected static string $relationship = 'languages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('lang')
                    ->options(Language::all()->pluck('name', 'code'))
                    ->required(),
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
            ])
            ->actions([
                \App\Helper\Actions::EditAction(fn($record) => $record),
                \App\Helper\Actions::DeleteAction()
            ]);
    }
}
