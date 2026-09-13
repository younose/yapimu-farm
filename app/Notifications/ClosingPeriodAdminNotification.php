<?php

namespace App\Notifications;

use App\Models\ClosingPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClosingPeriodAdminNotification extends Notification
{
    use Queueable;

    public $message = null;

    public $closingPeriod = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(ClosingPeriod $closingPeriod)
    {
        $this->message = 'Periode %s telah ditutup dengan keuntungan sebesar %s';
        $this->message = sprintf($this->message, $closingPeriod->period->name, idrFormat($closingPeriod->dividend));
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
            'closingPeriod' => $this->closingPeriod,
            'media' => (object) [
                'color' => 'media-primary',
                'icon' => 'fas fa-file-invoice-dollar fa-fw',
            ],
        ];
    }
}
