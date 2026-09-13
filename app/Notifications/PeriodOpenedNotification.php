<?php

namespace App\Notifications;

use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PeriodOpenedNotification extends Notification
{
    use Queueable;

    public $period = null;

    public $message = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(Period $period)
    {
        $this->message = 'Periode %s telah dibuka dengan masa berlaku %s s.d %s';
        $this->message = sprintf($this->message, $period->name, Carbon::parse($period->start_date)->format('d M Y'), Carbon::parse($period->end_date)->format('d M Y'));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'period' => $this->period,
            'media' => (object) [
                'color' => 'media-warning',
                'icon' => 'fas fa-calendar-check fa-fw',
            ],
        ];
    }
}
