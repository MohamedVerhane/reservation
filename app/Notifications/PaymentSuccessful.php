<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessful extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment,
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payment = $this->payment;
        $reservation = $payment->reservation;

        return (new MailMessage)
            ->subject(__('auth.notif_payment_success_subject'))
            ->greeting(__('auth.notif_hello', ['name' => $notifiable->name]))
            ->line(__('auth.notif_payment_success_line1'))
            ->line(__('auth.notif_amount') . ': ' . __('auth.notif_currency_format', ['amount' => number_format($payment->amount, 2)]))
            ->line(__('auth.notif_method_with_value', ['value' => $payment->method_label]))
            ->line(__('auth.notif_booking_id') . ': ' . __('auth.notif_booking_id_format', ['id' => $reservation->id]))
            ->line(__('auth.notif_hotel_with_value', ['value' => $reservation->hotel->name]))
            ->action(__('auth.notif_view_invoice'), route('customer.invoices'));
    }

    public function toArray(object $notifiable): array
    {
        $payment = $this->payment;
        $reservation = $payment->reservation;

        return [
            'type'           => 'payment_successful',
            'payment_id'     => $payment->id,
            'reservation_id' => $reservation->id,
            'amount'         => $payment->amount,
            'method'         => $payment->method,
            'method_label'   => $payment->method_label,
            'hotel_name'     => $reservation->hotel->name,
            'transaction_id' => $payment->transaction_id,
            'message'        => __('auth.notif_payment_success_line1'),
        ];
    }
}
