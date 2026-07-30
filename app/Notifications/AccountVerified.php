<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountVerified extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'type'=>'account_verified',

            'message'=>
                'تمت مراجعة مستنداتك والموافقة عليها'

        ];
    }
}