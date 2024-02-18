<?php

namespace App\Filament\Seller\Resources\ProductResource\RelationManagers;

use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class LanguageRelationManager extends RelationManager
{
    protected static string $relationship = 'language';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('unit')
                        ->required(),
                    Forms\Components\TextInput::make('badge')
                        ->required(),
                ]),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\RichEditor::make('overview')
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('meta_title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('lang')
                    ->options(Language::all()->pluck('name', 'code'))
                    ->searchable()
                    ->required(),
                Forms\Components\Textarea::make('meta_description')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                // Tables\Columns\TextColumn::make('meta_title'),
                // Tables\Columns\TextColumn::make('meta_description'),
                Tables\Columns\TextColumn::make('lang'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()
                    ->modalWidth('2xl')
                    ->slideOver(),
            ])
            ->actions([
                \App\Helper\Actions::EditAction()
                    ->modalWidth('2xl')
                    ->slideOver(),
                \App\Helper\Actions::DeleteAction(),
            ]);
    }
}
