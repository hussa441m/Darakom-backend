<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public $projectId,
        public $invitationId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'project_invitation',
            'message' => 'تمت دعوتك للمشاركة في مشروع خاص',
            'project_id' => $this->projectId,
            'invitation_id' => $this->invitationId,
        ];
    }
}