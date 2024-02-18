<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\RegisterUserResource\Pages;
use App\Filament\Admin\Resources\Admin\RegisterUserResource\RelationManagers;
use App\Helper\Setting;
use App\Models\Cancellation;
use App\Models\Cart;
use App\Models\CompareList;
use App\Models\Customer;
use App\Models\Helper\FileHelper;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use App\Models\UserAddress;
use App\Models\UserWishlist;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegisterUserResource extends Resource
{

    protected static ?string $model = Customer::class;
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'tabler-users';
    protected static ?int $navigationSort = 4;

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
                Tables\Columns\TextColumn::make('verified')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        1 => 'success',
                        0 => 'danger'
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        1 => 'Verified',
                        0 => 'Not Verified'
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::CustomDelete()->action(function($record) {
                    
                    try {

                            Cart::where('user_id', $record->id)->delete();
                            UserWishlist::where('user_id', $record->id)->delete();
                            CompareList::where('user_id', $record->id)->delete();

                            // Ordered products delete
                            $orderedProducts = OrderedProduct::leftJoin('orders', 'ordered_products.order_id', '=', 'orders.id')
                                ->where('orders.user_id', $record->id);

                            $orderedProducts->delete();

                            // Cancellation message  delete
                            $cancellation = Cancellation::leftJoin('orders', 'cancellations.order_id', '=', 'orders.id')
                                ->where('orders.user_id', $record->id);

                            $cancellation->delete();

                            Order::where('user_id', $record->id)->delete();

                            // Review delete
                            $reviewImages = ReviewImage::leftJoin('rating_reviews', 'review_images.rating_review_id', '=', 'rating_reviews.id')
                                ->where('rating_reviews.user_id', $record->id);

                            $rimages = $reviewImages->get();
                            foreach ($rimages as $img) {
                                FileHelper::deleteFile($img->image);
                            }

                            $reviewImages->delete();

                            RatingReview::where('user_id', $record->id)->delete();

                            // Address delete
                            UserAddress::where('user_id', $record->id)->delete();
                            
                            $record->delete();

                        Notification::make()
                            ->title('Deleted')
                            ->body('User Successfully Deleted.')
                            ->success()
                            ->send();

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\RegisterUserResource\Pages\ListRegisterUsers::route('/'),
            //            'create' => Pages\CreateRegisterUser::route('/create'),
            //            'edit' => Pages\EditRegisterUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return 'Registered User';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Registered Users';
    }
}
