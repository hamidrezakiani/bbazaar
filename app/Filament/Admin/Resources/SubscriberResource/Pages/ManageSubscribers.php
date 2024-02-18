<?php

namespace App\Filament\Admin\Resources\SubscriberResource\Pages;

use App\Filament\Admin\Resources\SubscriberResource;
use App\Models\Helper\MailHelper;
use App\Models\SubscriptionEmail;
use App\Models\SubscriptionEmailFormat;
use Filament\Actions;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Wallo\FilamentSelectify\Components\ButtonGroup;

class ManageSubscribers extends ManageRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send')
                ->icon('tabler-send')
                ->action(function ($data) {
                    try {

                        $subscriptionEmailFormat = SubscriptionEmailFormat::find($data['email_format']);
                        if (is_null($subscriptionEmailFormat)) {
                            Notification::make()
                                ->title('Error')
                                ->body('No Email Format Found')
                                ->danger()
                                ->send();
                        }

                        $subscribers = SubscriptionEmail::get();

                        if (count($subscribers) < 1) {
                            Notification::make()
                                ->title('Error')
                                ->body('No Subscriber Found')
                                ->danger()
                                ->send();
                        }

                        $subscribers = MailHelper::sendingSubscriptionEmail($subscriptionEmailFormat->subject,
                            $subscriptionEmailFormat->body, $subscribers);
                        Notification::make()
                            ->title('Success')
                            ->body('Email Sent')
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
                })
                ->form([
                    ButtonGroup::make('email_format')
                        ->gridDirection('row')
                        ->options(SubscriptionEmailFormat::all()->pluck('title', 'id')),
//                    Radio::make('email_format')
//                        ->hiddenLabel()
//                        ->options(SubscriptionEmailFormat::all()->pluck('title', 'id'))
                ])
                ->modalWidth('lg')
                ->label('Send Mail'),
        ];
    }
}
