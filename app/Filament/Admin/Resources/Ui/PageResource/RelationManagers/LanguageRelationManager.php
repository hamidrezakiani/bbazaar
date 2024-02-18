<?php

namespace App\Filament\Admin\Resources\Ui\PageResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Language;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Admin\Resources\Ui\PageResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class LanguageRelationManager extends RelationManager
{
    protected static string $relationship = 'Language';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lang')
                    ->options(Language::all()->pluck('name', 'code'))
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->columnSpanFull()
                    ->required(),
//                Forms\Components\Select::make('page_from_component')
//                    ->options([
//                        1 => 'Yes',
//                        2 => 'No'
//                    ])->default(1)
//                    ->reactive()
//                    ->columnSpanFull()
//                    ->required(),
                //                    FormInput\Components\Select::make('component')
                //                        ->options([
                //                            'Contact' => 'Contact',
                //                            'Sitemap' => 'Sitemap'
                //                        ])->reactive()
                //                        ->default(function(FormInput\Get $get){
                //                            if($get('page_from_component') == 1){
                //                                return $get('description');
                //                            }
                //                        })
                //                        ->hidden(fn (FormInput\Get $get) => $get('page_from_component') == 2)
                //                        ->afterStateUpdated(fn (FormInput\Set $set,$state) => $set('description','<p>'.$state.'</p>')),
                Forms\Components\Group::make()->schema(fn (Forms\Get $get) => PageResource::getFaq($get('title')))->columnSpanFull(),
                //                    ->hidden(fn (FormInput\Get $get) => $get('page_from_component') == 1),
                Forms\Components\TextInput::make('meta_title')->columnSpanFull(),
                Forms\Components\Textarea::make('meta_description')->columnSpanFull(),

                Forms\Components\Hidden::make('admin_id')->dehydrateStateUsing(fn() => Auth::user()->id)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('lang'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \App\Helper\Actions::addLanguageAction()
                    ->createAnother(false)
                    ->modalHeading('Create Page')
                    ->slideOver()
                    ->modalWidth('4xl'),
            ])
            ->actions([
                \App\Helper\Actions::EditAction()
                    ->modalHeading(fn($record) => "Edit $record->title")
                    ->slideOver()
                    ->modalWidth('4xl'),
                \App\Helper\Actions::DeleteAction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
