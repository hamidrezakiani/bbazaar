<?php

namespace App\Filament\Admin\Resources\Ui;

use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Admin\Resources\Admin\Ui\PageResource\Pages;
use App\Filament\Admin\Resources\Admin\Ui\PageResource\RelationManagers;
use App\Filament\Admin\Resources\Ui\PageResource\RelationManagers\LanguageRelationManager;
use App\Helper\Setting;
use App\Models\Page;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationGroup = 'UI';
    protected static ?string $navigationIcon = 'tabler-file-text';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Form')->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\TextInput::make('slug')->required(),
                    ])->columns(2),
                    Forms\Components\Select::make('page_from_component')
                        ->options([
                            1 => 'Yes',
                            2 => 'No'
                        ])->default(1)
                        ->reactive()
                        ->required(),
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
                    Forms\Components\Group::make()->schema(fn (Forms\Get $get) => self::getFaq($get('title'))),
                    //                    ->hidden(fn (FormInput\Get $get) => $get('page_from_component') == 1),
                    Forms\Components\TextInput::make('meta_title'),
                    Forms\Components\Textarea::make('meta_description'),
                ]),
                Forms\Components\Hidden::make('admin_id')->dehydrateStateUsing(fn() => Auth::user()->id)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::DeleteAction(),
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            LanguageRelationManager::make()
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\Ui\PageResource\Pages\ListPages::route('/'),
            'create' => \App\Filament\Admin\Resources\Ui\PageResource\Pages\CreatePage::route('/create'),
            'edit' => \App\Filament\Admin\Resources\Ui\PageResource\Pages\EditPage::route('/{record}/edit'),
        ];
    }
    
    public static function getFaq($editor):array{
        $editorForm = [];
        $checkEditor = trim($editor);
        if($checkEditor === 'FAQ'){
            $editorForm[] = Forms\Components\MarkdownEditor::make('description');
        } elseif($checkEditor !== 'Contact' AND $checkEditor !== 'Sitemap') {
            $editorForm[] =  TinyEditor::make('description');
        }
        return $editorForm;
    }
}
