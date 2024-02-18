<?php

namespace App\Filament\Admin\Resources\CategoryResource\RelationManagers;

use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryLangRelationManager extends RelationManager
{
    protected static string $relationship = 'languages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('meta_title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('meta_description')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Select::make('lang')
                    ->label('Language')
                    ->options(Language::all()->pluck('name', 'code'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('meta_title'),
                Tables\Columns\TextColumn::make('meta_description'),
                Tables\Columns\TextColumn::make('lang'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()->createAnother(false),
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::DeleteAction(),
            ]);
    }
}
