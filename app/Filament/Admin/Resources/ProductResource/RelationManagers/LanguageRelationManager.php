<?php

namespace App\Filament\Admin\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Language;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Resources\RelationManagers\RelationManager;

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
                    Forms\Components\TextInput::make('badge'),
                ]),
                TiptapEditor::make('description')
                    ->columnSpan('full')
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
                    ->modalWidth('5xl'),
            ])
            ->actions([
                \App\Helper\Actions::EditAction()
                    ->modalWidth('5xl'),
                \App\Helper\Actions::DeleteAction(),
            ]);
    }
}
