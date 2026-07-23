<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RejectOffer extends Notification
{
    use Queueable;

    public function __construct(
        public $offerId,
        public $projectId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_rejected',
            'message' => 'تم رفض عرضك في المشروع',
            'offer_id' => $this->offerId,
            'project_id' => $this->projectId,
        ];
    }
}