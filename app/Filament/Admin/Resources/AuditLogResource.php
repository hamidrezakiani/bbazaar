<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditLogResource\Pages;
use App\Filament\Admin\Resources\AuditLogResource\RelationManagers;
use App\Helper\Setting;
use App\Models\Audit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mohammadhprp\IPToCountryFlagColumn\Columns\IPToCountryFlagColumn;

class AuditLogResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'tabler-report';
    protected static ?string $navigationGroup = "Setting";
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Logs';

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
                Tables\Columns\TextColumn::make('user_type'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('auditable_type'),
                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('Auditable')
                    ->words(10),
                IPToCountryFlagColumn::make('ip_address')->flagPosition('left')
                ->label('IP Address'),
                Tables\Columns\TextColumn::make('event')->badge(),
                Tables\Columns\TextColumn::make('created_at'),
                Tables\Columns\TextColumn::make('updated_at'),
            ])
            ->filters([
                //
            ])
            ->actions([

            ])
            ->headerActions([
            ])
            ->paginationPageOptions(Setting::pagination());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
