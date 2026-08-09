<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UrgentProject extends Notification
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
            'type' => 'urgent_project',
            'message' => 'يوجد مشروع مستعجل يحتاج إلى تقديم عرضك بسرعة',
            'project_id' => $this->projectId,
        ];
    }
}