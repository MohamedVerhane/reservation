<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingAlert extends Notification implements ShouldQueue
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
        $guest = $reservation->user;

        return (new MailMessage)
            ->subject(__('auth.notif_admin_new_booking_subject'))
            ->line(__('auth.notif_admin_new_booking_line1'))
            ->line(__('auth.notif_booking_id') . ': ' . __('auth.notif_booking_id_format', ['id' => $reservation->id]))
            ->line(__('auth.notif_guest') . ': ' . ($guest->name ?? __('auth.notif_na')))
            ->line(__('auth.notif_hotel_with_value', ['value' => $reservation->hotel->name]))
            ->line(__('auth.notif_room_with_value', ['value' => ($reservation->room->display_name ?? $reservation->room->room_number)]))
            ->line(__('auth.notif_check_in') . ': ' . $reservation->check_in->format(__('auth.date_format')))
            ->line(__('auth.notif_check_out') . ': ' . $reservation->check_out->format(__('auth.date_format')))
            ->line(__('auth.notif_total') . ': ' . __('auth.notif_currency_format', ['amount' => number_format($reservation->total_price, 2)]))
            ->action(__('auth.notif_admin_view_booking'), route('admin.reservations.show', $reservation));
    }

    public function toArray(object $notifiable): array
    {
        $reservation = $this->reservation;

        return [
            'type'           => 'new_booking',
            'reservation_id' => $reservation->id,
            'guest_name'     => $reservation->user->name ?? __('auth.notif_na'),
            'guest_email'    => $reservation->user->email ?? '',
            'hotel_name'     => $reservation->hotel->name,
            'room_name'      => $reservation->room->display_name ?? $reservation->room->room_number,
            'check_in'       => $reservation->check_in->format('Y-m-d'),
            'check_out'      => $reservation->check_out->format('Y-m-d'),
            'total_price'    => $reservation->total_price,
            'message'        => __('auth.notif_admin_new_booking_line1'),
        ];
    }
}
