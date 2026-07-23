<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AcceptProvider extends Notification
{
    use Queueable;

    public function __construct(
        public $projectId,
        public $offerId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'accept_provider',
            'message' => 'تم قبول مشاركتك ',
            'project_id' => $this->projectId,
            'offer_id' => $this->offerId,
        ];
    }
}