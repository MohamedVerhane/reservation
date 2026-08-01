<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
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
        $hotel = $reservation->hotel;
        $room = $reservation->room;

        return (new MailMessage)
            ->subject(__('auth.notif_booking_confirmed_subject'))
            ->greeting(__('auth.notif_hello', ['name' => $notifiable->name]))
            ->line(__('auth.notif_booking_confirmed_line1'))
            ->line(__('auth.notif_hotel_with_value', ['value' => $hotel->name]))
            ->line(__('auth.notif_room_with_value', ['value' => ($room->display_name ?? $room->room_number)]))
            ->line(__('auth.notif_check_in') . ': ' . $reservation->check_in->format(__('auth.date_format')))
            ->line(__('auth.notif_check_out') . ': ' . $reservation->check_out->format(__('auth.date_format')))
            ->line(__('auth.notif_total') . ': ' . __('auth.notif_currency_format', ['amount' => number_format($reservation->total_price, 2)]))
            ->line(__('auth.notif_booking_id') . ': ' . __('auth.notif_booking_id_format', ['id' => $reservation->id]))
            ->action(__('auth.notif_view_booking'), route('frontend.booking.my-reservations'))
            ->line(__('auth.notif_thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        $reservation = $this->reservation;

        return [
            'type'           => 'booking_confirmed',
            'reservation_id' => $reservation->id,
            'hotel_name'     => $reservation->hotel->name,
            'hotel_slug'     => $reservation->hotel->slug,
            'room_name'      => $reservation->room->display_name ?? $reservation->room->room_number,
            'check_in'       => $reservation->check_in->format('Y-m-d'),
            'check_out'      => $reservation->check_out->format('Y-m-d'),
            'total_price'    => $reservation->total_price,
            'message'        => __('auth.notif_booking_confirmed_line1'),
        ];
    }
}
