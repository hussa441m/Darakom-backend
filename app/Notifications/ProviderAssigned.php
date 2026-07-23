<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProviderAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public $projectId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'provider_assigned',
            'message' => 'تم تعيينك لتنفيذ المشروع',
            'project_id' => $this->projectId,
        ];
    }
}