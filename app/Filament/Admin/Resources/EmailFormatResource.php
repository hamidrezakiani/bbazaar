<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\EmailFormatResource\Pages;
use App\Filament\Admin\Resources\Admin\EmailFormatResource\RelationManagers;
use App\Helper\Setting;
use App\Models\SubscriptionEmailFormat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Auth;

class EmailFormatResource extends Resource
{
    public string $renderedHtml = '';
    protected static ?string $model = SubscriptionEmailFormat::class;
    protected static ?string $modelLabel = 'Email Format';
    protected static ?string $pluralModelLabel = 'Email Formats';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationIcon = 'tabler-mail-opened';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\TextInput::make('subject')->required(),
                Forms\Components\Section::make('Body')->schema([
                    Forms\Components\MarkdownEditor::make('body')
                        ->hiddenLabel()
                        ->required()
                ])->collapsible()
                    ->collapsed(),
                Forms\Components\Section::make('HTML Render')->schema([
                    Forms\Components\ViewField::make('preview')
                        ->view('filament.resources.components.email-format.email-format-rendered')
                ])->hiddenOn('create'),
                Forms\Components\Hidden::make('admin_id')->dehydrateStateUsing(fn() => Auth::user()->id)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('subject'),
                Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction()
                    ->slideOver()
                    ->modalWidth('5xl'),
                \App\Helper\Actions::DeleteAction(),
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\EmailFormatResource\Pages\ManageEmailFormats::route('/'),
        ];
    }

    public function rendereHtml($data)
    {
        $this->html = $data;
    }
}
