<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ChangeState extends Notification
{
    use Queueable;


    public function __construct(
        public string $state
    ) {}


    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'account_status_changed',
            'message' => "تم تعديل حالة حسابك إلى {$this->state}",
            'state' => $this->state,
        ];
    }
}