<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ActivityNotification extends Notification
{
    use Queueable;

    protected $activity;

    public function __construct($activity)
    {
        $this->activity = $activity;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->activity->description,
            'time' => $this->activity->created_at,
            'url' => '#', // আপনার রাউট অনুযায়ী পরিবর্তন করুন
        ];
    }
}