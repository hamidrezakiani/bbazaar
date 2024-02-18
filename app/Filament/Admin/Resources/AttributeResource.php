<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\AttributeResource\Pages;
use App\Filament\Admin\Resources\Admin\AttributeResource\RelationManagers;
use App\Filament\Admin\Resources\AttributeResource\RelationManagers\LanguageRelationManager;
use App\Helper\Setting;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationIcon = 'tabler-tags';

    protected static ?string $navigationGroup =  'Product';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attribute Form')
                    ->schema([
                        Forms\Components\TextInput::make('title'),
                        Forms\Components\Section::make('Value')->schema([
                            Forms\Components\Repeater::make('value')
                                ->relationship('values')
                                ->hiddenLabel()
                                ->schema([
                                    Forms\Components\TextInput::make('title')->required(),
                                    Forms\Components\Hidden::make('admin_id')
                                        // ->disabled(fn ($context) => $context ===  'edit')
                                        ->dehydrateStateUsing(fn () => Auth::user()->id)
                                ])->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                ->grid(3),
                        ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('values.title')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])->paginationPageOptions(Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            LanguageRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\AttributeResource\Pages\ListAttributes::route('/'),
            'create' => \App\Filament\Admin\Resources\AttributeResource\Pages\CreateAttribute::route('/create'),
            'edit' => \App\Filament\Admin\Resources\AttributeResource\Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
