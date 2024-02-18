<?php

namespace App\Filament\Admin\Resources;


use App\Filament\Admin\Resources\Admin\BundleDealResource\Pages;
use App\Filament\Admin\Resources\Admin\BundleDealResource\RelationManagers;
use App\Filament\Admin\Resources\BundleDealResource\RelationManagers\LanguageRelationManager;
use App\Helper\Setting;
use App\Models\BundleDeal;
use App\Models\BundleDealLang;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BundleDealResource extends Resource
{
    protected static ?string $model = BundleDeal::class;
    protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationIcon = 'tabler-stack-2';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bundle Form')->schema([
                    Forms\Components\TextInput::make('title')
                        ->maxLength(255),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('buy')->required(),
                        Forms\Components\TextInput::make('free')->required(),
                    ])
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
                Tables\Columns\TextColumn::make('buy'),
                Tables\Columns\TextColumn::make('free'),
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
                            $product = Product::where('bundle_deal_id', $record->id)->first();
                            if ($product) {
                                Notification::make()
                                    ->title('Unable to Delete')
                                    ->body("This {$record->title} record used by Product.")
                                    ->danger()
                                    ->send();
                            } else {
                                BundleDealLang::where('bundle_deal_id', $record->id)->delete();
                                $record->delete();
                                Notification::make()
                                    ->title('Success')
                                    ->body("Deleted Successfully")
                                    ->danger()
                                    ->send();
                            }

                        } catch (\Exception $ex) {
                            Notification::make()
                                ->title('Error')
                                ->body($ex->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->paginationPageOptions(Setting::pagination());
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
            'index' => \App\Filament\Admin\Resources\BundleDealResource\Pages\ListBundleDeals::route('/'),
            'create' => \App\Filament\Admin\Resources\BundleDealResource\Pages\CreateBundleDeal::route('/create'),
            'edit' => \App\Filament\Admin\Resources\BundleDealResource\Pages\EditBundleDeal::route('/{record}/edit'),
        ];
    }
}
