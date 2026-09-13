<?php

namespace App\Notifications;

use App\Models\Dividend;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClosingPeriodInvestorNotification extends Notification
{
    use Queueable;

    public $dividend = null;

    public $message = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(Dividend $dividend)
    {
        $this->dividend = $dividend;

        $this->message = 'Selamat, Anda telah mendapatkan dividen sebesar '.idrFormat($dividend->total_dividend).' dari periode '.$dividend->period->name;
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
            'dividend' => $this->dividend,
            'media' => (object) [
                'color' => 'media-primary',
                'icon' => 'fas fa-money-bill-trend-up fa-fw',
            ],
        ];
    }
}
