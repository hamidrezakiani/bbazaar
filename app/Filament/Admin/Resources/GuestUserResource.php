<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Admin\GuestUserResource\Pages;
use App\Filament\Admin\Resources\Admin\GuestUserResource\RelationManagers;
use App\Helper\Setting;
use App\Models\Cancellation;
use App\Models\Cart;
use App\Models\GuestUser;
use App\Models\Helper\FileHelper;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use App\Models\UserAddress;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GuestUserResource extends Resource
{
    protected static ?string $model = GuestUser::class;
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
                Tables\Columns\TextColumn::make('id')
                ->label('ID'),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn ($record) => empty($record->name) ? 'N/A' : $record->name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                ->formatStateUsing(fn ($state) => $state ?? 'N/A'),
                Tables\Columns\TextColumn::make('user_token'),
                Tables\Columns\TextColumn::make('created_at')
                ->formatStateUsing(fn ($state) => \App\Helper\Setting::dateTime($state))
            ])
            ->filters([
                //
            ])
            ->actions([
                \App\Helper\Actions::CustomDelete()->action(function ($record) {
                    try {

                        $user = GuestUser::find($record->id);

                        Cart::where('user_token', $user->user_token)
                            ->where('user_id', '!=', null)
                            ->update([
                                'user_token' => null
                            ]);

                        Cart::where('user_token', $user->user_token)
                            ->where('user_id', null)
                            ->delete();

                        // Ordered products delete
                        $orderedProducts = OrderedProduct::leftJoin('orders', 'ordered_products.order_id', '=', 'orders.id')
                            ->where('orders.user_token', $user->user_token)
                            ->where('orders.user_id', null);

                        $orderedProducts->delete();

                        // Cancellation message  delete

                        Cancellation::where('user_token', $user->user_token)
                            ->where('user_id', '!=', null)
                            ->update([
                                'user_token' => null
                            ]);

                        $cancellation = Cancellation::leftJoin('orders', 'cancellations.order_id', '=', 'orders.id')
                            ->where('orders.user_token', $user->user_token)
                            ->where('orders.user_id', null);

                        $cancellation->delete();

                        Order::where('user_token', $user->user_token)
                            ->where('user_id', '!=', null)
                            ->update([
                                'user_token' => null
                            ]);

                        Order::where('user_token', $user->user_token)
                            ->where('user_id', null)
                            ->delete();

                        // Review delete
                        $reviewImages = ReviewImage::leftJoin('rating_reviews', 'review_images.rating_review_id', '=', 'rating_reviews.id')
                            ->where('rating_reviews.user_token', $user->user_token)
                            ->where('rating_reviews.user_id', null);

                        $rimages = $reviewImages->get();
                        foreach ($rimages as $img) {
                            FileHelper::deleteFile($img->image);
                        }

                        $reviewImages->delete();

                        RatingReview::where('user_token', $user->user_token)
                            ->where('user_id', '!=', null)
                            ->update([
                                'user_token' => null
                            ]);

                        RatingReview::where('user_token', $user->user_token)
                            ->where('user_id', null)
                            ->delete();

                        // Address delete
                        UserAddress::where('user_token', $user->user_token)
                            ->where('user_id', '!=', null)
                            ->update([
                                'user_token' => null
                            ]);

                        UserAddress::where('user_token', $user->user_token)
                            ->where('user_id', null)
                            ->delete();

                        if ($user->delete()) {
                            Notification::make()
                                ->title('Success')
                                ->body('Guest User Deleted Successfully')
                                ->success()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\GuestUserResource\Pages\ListGuestUsers::route('/'),
            //            'create' => Pages\CreateGuestUser::route('/create'),
            //            'edit' => Pages\EditGuestUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return 'Guest User';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Guest Users';
    }
}
