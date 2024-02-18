<?php

namespace App\Filament\Admin\Resources\StoreResource\RelationManagers;

use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LanguageRelationManager extends RelationManager
{
    protected static string $relationship = 'Language';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lang')
                    ->label('Language')
                    ->searchable()
                    ->options(Language::all()->pluck('name', 'code')),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('meta_title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('meta_description')
                    ->required()
                    ->maxLength(255),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('meta_title'),
                Tables\Columns\TextColumn::make('meta_description'),
                Tables\Columns\TextColumn::make('lang'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()
                    ->createAnother(false)
                    ->modalHeading('Add Language')
                    ->modalWidth('xl'),
            ])
            ->actions([
                \App\Helper\Actions::EditAction()
                    ->modalWidth('xl'),
                \App\Helper\Actions::DeleteAction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
