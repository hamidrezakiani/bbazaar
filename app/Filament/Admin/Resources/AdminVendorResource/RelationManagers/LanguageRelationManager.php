<?php

namespace App\Filament\Admin\Resources\AdminVendorResource\RelationManagers;

use App\Models\GlobalLanguage;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LanguageRelationManager extends RelationManager
{
    protected static string $relationship = 'language';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('name')
                    ->live(onBlur: true)
                    ->searchable()
                    ->options(GlobalLanguage::all()->pluck('name', 'code'))
                    ->afterStateUpdated(fn($state, Set $set) => $set('code', $state))
                    ->required(),
                TextInput::make('code')->readOnly()->required(),
                Select::make('direction')->options([
                    'ltr' => 'LTR',
                    'rtl' => 'RTL'
                ])->required(),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('direction'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                    })->formatStateUsing(fn($state): string => $state === 1 ? 'Active' : 'Inactive'),

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
