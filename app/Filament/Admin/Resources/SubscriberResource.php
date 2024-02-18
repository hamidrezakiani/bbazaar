<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\SubscriberResource\Pages;
use App\Filament\Admin\Resources\Admin\SubscriberResource\RelationManagers;
use App\Helper\Setting;
use App\Models\SubscriptionEmail;
use App\Models\SubscriptionEmailFormat;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriberResource extends Resource
{
    protected static ?string $model = SubscriptionEmail::class;

    protected static ?string $navigationGroup = 'Marketing';
    protected static ?string $navigationIcon = 'tabler-thumb-up';
    protected static ?string $pluralModelLabel = 'Subscribers';
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
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::DeleteAction(),
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\SubscriberResource\Pages\ManageSubscribers::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Subscriber';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Subscribers';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
