<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\TaxRulesResource\Pages;
use App\Filament\Admin\Resources\Admin\TaxRulesResource\RelationManagers;
use App\Filament\Admin\Resources\TaxRulesResource\RelationManagers\LanguageRelationManager;
use App\Models\Product;
use App\Models\Setting;
use App\Models\TaxRuleLang;
use App\Models\TaxRules;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaxRulesResource extends Resource
{
    protected static ?string $model = TaxRules::class;
    protected static ?string $navigationIcon = 'tabler-receipt-tax';
    protected static ?string $navigationGroup = 'Product';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tax Rule Form')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required(),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('price')->required(),
                            Forms\Components\Select::make('type')
                                ->options([
                                    1 => 'Flat',
                                    2 => 'Percent'
                                ])
                                ->required(),
                        ]),
                        Forms\Components\Hidden::make('admin_id')
                            ->dehydrateStateUsing(fn() => Auth::user()->id)

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('price')->label('Amount'),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn($state) => $state == 1 ? 'Flat' : 'Percentage'),
                Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()
                    ->action(function ($record) {
                        try {
                            DB::transaction(function () use ($record) {
                                $product = Product::where('tax_rule_id', $record->id)->exists();
                                if ($product) {
                                    throw new Exception('Tax Rule is used by a product.');
                                }

                                TaxRuleLang::where('tax_rule_id', $record->id)->delete();
                                $record->delete();

                                Notification::make()
                                    ->title('Success')
                                    ->body('Tax Rule Successfully Deleted.')
                                    ->success()
                                    ->send();
                            });
                        } catch (\Exception $ex) {
                            Notification::make()
                                ->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
            ]);
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
            'index' => \App\Filament\Admin\Resources\TaxRulesResource\Pages\ListTaxRules::route('/'),
            'create' => \App\Filament\Admin\Resources\TaxRulesResource\Pages\CreateTaxRules::route('/create'),
            'edit' => \App\Filament\Admin\Resources\TaxRulesResource\Pages\EditTaxRules::route('/{record}/edit'),
        ];
    }
}
