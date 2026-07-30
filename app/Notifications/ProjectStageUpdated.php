<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectStageUpdated extends Notification
{
    use Queueable;

    public function __construct(
        public $projectId,
        public $stepName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'type' => 'project_stage',

            'message' =>
                "تم تأكيد المرحلة {$this->stepName}",

            'project_id' => $this->projectId,

        ];
    }
}