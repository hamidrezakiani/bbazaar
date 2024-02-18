<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\AdminVendorResource\Pages;
use App\Filament\Admin\Resources\Admin\AdminVendorResource\RelationManagers;
use App\Filament\Admin\Resources\AdminVendorResource\RelationManagers\LanguageRelationManager;
use App\Helper\Setting;
use App\Models\Admin;
use App\Models\Language;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreLang;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WithdrawalAccount;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mohammadhprp\IPToCountryFlagColumn\Columns\IPToCountryFlagColumn;
use Spatie\Permission\Models\Role;

class AdminVendorResource extends Resource
{
    protected static ?string $model = Admin::class;
    protected static ?string $modelLabel = 'Admin & Vendor';
    protected static ?string $pluralModelLabel = 'Admins & Vendors';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'tabler-users';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Admin/Vendor Form')->schema([
                    Forms\Components\Group::make()->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('username')->required(),
                    ])->columns(2),
                    Forms\Components\TextInput::make('email')->required(),
                    Forms\Components\Group::make()->schema([
                        Forms\Components\TextInput::make('password')
                            ->same('passwordConfirmation')
                            ->password()
                            ->autocomplete('new-password')
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->password()
                            ->dehydrated(false)
                            ->maxLength(255)
                    ])->columns(2)->hiddenOn('edit'),
                    Forms\Components\CheckboxList::make('roles')
                        ->relationship('roles', 'name')
                        ->reactive()
                        ->searchable(),
                    Forms\Components\TextInput::make('commission')
                        ->visible(function (Forms\Get $get): bool {
                            $roles = Role::where('id', $get('roles'))->first();
                            if ($roles?->name == 'Vendor') {
                                return true;
                            }
                            return false;
                        })
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('username'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('commission'),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('viewed')->badge(),
                Tables\Columns\TextColumn::make('roles.name')->badge(),
                Tables\Columns\ToggleColumn::make('status')
                    ->onIcon('tabler-user-check')
                    ->offIcon('tabler-user-off'),
                Tables\Columns\TextColumn::make('verified')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'success',
                    })->formatStateUsing(fn($state): string => $state === 1 ? 'Verified' : 'Unverified'),
                Tables\Columns\TextColumn::make('created_at')
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::EditAction(),
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {
                        DB::transaction(function () use ($record) {
                            if (Auth::user()->id == $record->id) {
                                throw new Exception('You cannot delete your own user account. Please contact Super Admin ');
                            }

                            $product = Product::where('admin_id', $record->id)->get()->first();
                            if ($product) {
                                throw new Exception('User ID is associated with a product');
                            }

                            $store = Store::where('admin_id', $record->id)->get();

                            foreach ($store as $s) {
                                StoreLang::where('store_id', $s->id)->delete();
                            }

                            Withdrawal::where('admin_id', $record->id)->delete();
                            WithdrawalAccount::where('admin_id', $record->id)->delete();
                            Store::where('admin_id', $record->id)->delete();
                            Language::where('admin_id', $record->id)->delete();

                            if ($record->delete()) {
                                Notification::make()
                                    ->title('Success')
                                    ->body('User Successfully Deleted.')
                                    ->success()
                                    ->send();
                            }

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
            'index' => \App\Filament\Admin\Resources\AdminVendorResource\Pages\ListAdminVendors::route('/'),
            'create' => \App\Filament\Admin\Resources\AdminVendorResource\Pages\CreateAdminVendor::route('/create'),
            'edit' => \App\Filament\Admin\Resources\AdminVendorResource\Pages\EditAdminVendor::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return 'Admin & Vendor';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Admin & Vendors';
    }
}
