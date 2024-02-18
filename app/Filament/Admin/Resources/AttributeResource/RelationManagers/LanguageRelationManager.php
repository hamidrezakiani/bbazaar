<?php

namespace App\Filament\Admin\Resources\AttributeResource\RelationManagers;

use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class LanguageRelationManager extends RelationManager
{
    protected static string $relationship = 'Language';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('lang')
                    ->options(Language::all()->pluck('name', 'code'))
                    ->required(),
                Forms\Components\Section::make('Value')->schema([
                    Forms\Components\Repeater::make('value')
                        ->relationship('values')
                        ->hiddenLabel()
                        ->schema([
                            Forms\Components\TextInput::make('title')->required(),
                            Forms\Components\Hidden::make('admin_id')
                                // ->disabled(fn ($context) => $context ===  'edit')
                                ->dehydrateStateUsing(fn() => Auth::user()->id)
                        ])->itemLabel(fn(array $state): ?string => $state['title'] ?? null)
                        ->grid(3),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('lang')
                ->label('Language'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
