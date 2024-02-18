<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\Order;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Mail;


class OrderObserver
{


    public function created(Order $order): void
    {

        $user = Admin::where('type', 'admin')->first();
        $customerName = '';

        if ($order->user !== null) {
            $customerName = $order->user->name;
        }

        if ($order->guest_user !== null) {
            $customerName = $order->guest_user->name;
        }

        $count = $order->countOrderedProduct;

        Notification::make()
            ->title('New Order')
            ->icon('heroicon-o-shopping-bag')
            ->body("{$customerName} has Order, OrderID: {$order->order}.")
            ->sendToDatabase($user);

        Mail::raw("{$customerName} has Order, OrderID: {$order->order}.", function ($message) {
            $message->to('it.support@bbazaar.af')
                ->subject('BBazaar New Order');
        });
    }
}
