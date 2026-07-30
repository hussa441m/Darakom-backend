<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewRating extends Notification
{
    use Queueable;

    public function __construct(
        public $projectId,
        public $rate
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [

            'type'=>'new_rating',

            'message'=>
                "حصلت على تقييم {$this->rate} نجوم",

            'project_id'=>$this->projectId,

            'rate'=>$this->rate

        ];
    }
}