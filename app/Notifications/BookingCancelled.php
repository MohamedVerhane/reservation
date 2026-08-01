<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public ?string $reason = null,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $reservation = $this->reservation;

        return (new MailMessage)
            ->subject(__('auth.notif_booking_cancelled_subject'))
            ->greeting(__('auth.notif_hello', ['name' => $notifiable->name]))
            ->line(__('auth.notif_booking_cancelled_line1'))
            ->line(__('auth.notif_hotel_with_value', ['value' => $reservation->hotel->name]))
            ->line(__('auth.notif_booking_id') . ': ' . __('auth.notif_booking_id_format', ['id' => $reservation->id]))
            ->line(__('auth.notif_refund_notice'))
            ->action(__('auth.notif_view_booking'), route('frontend.booking.my-reservations'));
    }

    public function toArray(object $notifiable): array
    {
        $reservation = $this->reservation;

        return [
            'type'           => 'booking_cancelled',
            'reservation_id' => $reservation->id,
            'hotel_name'     => $reservation->hotel->name,
            'hotel_slug'     => $reservation->hotel->slug,
            'check_in'       => $reservation->check_in->format('Y-m-d'),
            'check_out'      => $reservation->check_out->format('Y-m-d'),
            'total_price'    => $reservation->total_price,
            'reason'         => $this->reason,
            'message'        => __('auth.notif_booking_cancelled_line1'),
        ];
    }
}
