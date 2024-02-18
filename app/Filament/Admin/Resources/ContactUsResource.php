<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactUsResource\Pages;
use App\Filament\Admin\Resources\ContactUsResource\RelationManagers;
use App\Models\ContactUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;
    protected static ?string $modelLabel = 'Contact Us';
    protected static ?string $pluralModelLabel = 'Contact Us';

    protected static ?string $navigationIcon = 'tabler-message';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('subject'),
                Tables\Columns\TextColumn::make('message')->words(50)
                ->tooltip(fn ($state) => $state),
            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContactUs::route('/'),
        ];
    }
}
