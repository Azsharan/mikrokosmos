<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Nueva reserva'),
            'message' => __(':customer apartó :product (:quantity)', [
                'customer' => $this->reservation->name,
                'product' => $this->reservation->product?->name ?? __('Producto eliminado'),
                'quantity' => $this->reservation->quantity,
            ]),
            'code' => $this->reservation->code,
            'reservation_id' => $this->reservation->id,
            'product_id' => $this->reservation->product_id,
            'url' => route('admin.reservations.index'),
        ];
    }
}
