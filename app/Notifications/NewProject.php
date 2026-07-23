<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewProject extends Notification
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
            'type' => 'new_project',
            'message' => 'تم نشر مشروع جديد',
            'project_id' => $this->projectId,
        ];
    }
}