<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOffer extends Notification
{
    use Queueable;


    public function __construct(
        public $offerId,
        public $projectId,
        public $providerName
    ) {}


    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_offer',
            'message' => "عرض جديد من {$this->providerName}",
            'offer_id' => $this->offerId,
            'project_id' => $this->projectId,
        ];
    }
}