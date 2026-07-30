<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewComplaint extends Notification
{
    use Queueable;

    public function __construct(
        public $complaintId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'type'=>'new_complaint',

            'message'=>
                'قام أحد العملاء بتقديم شكوى ضدك',

            'complaint_id'=>$this->complaintId

        ];
    }
}