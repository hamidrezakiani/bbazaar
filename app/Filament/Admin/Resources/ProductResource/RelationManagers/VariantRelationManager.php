<?php

namespace App\Filament\Admin\Resources\ProductResource\RelationManagers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VariantRelationManager extends RelationManager
{
    protected static string $relationship = 'inventory';
    protected static ?string $label = 'Product Variant';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attribute')
                    ->options(Attribute::all()->pluck('title', 'id'))
                    ->live(),
                Forms\Components\CheckboxList::make('attribute_value')
                    ->options(function (Forms\Get $get) {
                        return AttributeValue::where('attribute_id', $get('attribute'))->get()->pluck('title', 'id');
                    })->gridDirection('row')
                    ->columns(['sm' => 2, 'md' => 3, 'lg' => 5])
                    ->inlineLabel()
                    ->hiddenLabel()
                    ->columnSpanFull(),
                Forms\Components\Grid::make()->schema(
                    [
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                    ]
                )

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
//                Tables\Columns\TextColumn::make('inventory_attributes.attribute_value.attribute.title'),
                Tables\Columns\TextColumn::make('inventory_attributes.attribute_value.title'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('price'),
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

    public function productCombination($options): array
    {
        $fields = [];

        $newCombinations = [];

        if (empty($combinations)) {
            foreach ($options as $optionId => $optionTitle) {
                $newCombinations[] = [$optionId => $optionTitle];
            }
        } else {
            foreach ($combinations as $combination) {
                foreach ($options as $optionId => $optionTitle) {
                    $newCombination = $combination;
                    $newCombination[$optionId] = $optionTitle;
                    $newCombinations[] = $newCombination;
                }
            }
        }
        return $newCombinations;
    }
}
