<?php

namespace App\Filament\Admin\Resources;

use App\Models\CollectionWithProduct;
use App\Models\ProductCollectionLang;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Tables;
use App\Helper\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ProductCollection;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Wallo\FilamentSelectify\Components\ButtonGroup;
use Wallo\FilamentSelectify\Components\ToggleButton;
use App\Filament\Admin\Resources\CollectionResource\Pages;
use App\Filament\Admin\Resources\CollectionResource\RelationManagers;
use App\Filament\Admin\Resources\CollectionResource\RelationManagers\CollectionLanguageRelationManager;

class CollectionResource extends Resource
{
    protected static ?string $model = ProductCollection::class;

    protected static ?string $modelLabel = 'Collections';
    protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Collection Form')->schema([
                    Forms\Components\TextInput::make('title')
                        ->lazy()
                        ->afterStateUpdated(fn(Forms\Set $set, $state) => $set('slug', $state))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')->required(),
                    Toggle::make('status')
                        ->inline(false)
                        ->onIcon('tabler-eye')
                        ->offIcon('tabler-eye-off'),
                ]),
                Forms\Components\Hidden::make('admin_id')
                    ->dehydrateStateUsing(fn() => Auth::user()->id)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('OrderStatus')
                    ->inline(false)
                    ->onIcon('tabler-eye')
                    ->offIcon('tabler-eye-off'),
//                Tables\Columns\TextColumn::make('status')
//                    ->badge()
//                    ->color(fn (string $state): string => match ($state) {
//                        '2' => 'warning',
//                        '1' => 'success',
//                    })->formatStateUsing(fn ($state): string => $state === 1 ? 'Public' : 'Private'),
                Tables\Columns\TextColumn::make('created_at')

            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(fn($record) => $record),
                \App\Helper\Actions::CustomDelete()
                    ->action(function ($record) {
                        try {
                            $productCollection = ProductCollection::find($record->id);
                            CollectionWithProduct::where('product_collection_id', $record->id)->delete();
                            ProductCollectionLang::where('product_collection_id', $record->id)->delete();
                            $productCollection->delete();
                            Notification::make()
                                ->title('Success')
                                ->body('Collection Deleted Successfully')
                                ->send();
                        } catch (\Exception $ex) {
                            Notification::make()
                                ->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            CollectionLanguageRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CollectionResource\Pages\ListCollections::route('/'),
            'create' => \App\Filament\Admin\Resources\CollectionResource\Pages\CreateCollection::route('/create'),
            'edit' => \App\Filament\Admin\Resources\CollectionResource\Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
