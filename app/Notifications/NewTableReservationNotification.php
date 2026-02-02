<?php

namespace App\Notifications;

use App\Models\TableReservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTableReservationNotification extends Notification
{
    use Queueable;

    public function __construct(public TableReservation $reservation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Nueva reserva de mesa'),
            'message' => __(':customer reservó la mesa :table para :size personas', [
                'customer' => $this->reservation->shopUser?->name ?? __('Cliente registrado'),
                'table' => $this->reservation->table_number,
                'size' => $this->reservation->party_size,
            ]),
            'code' => $this->reservation->code,
            'reservation_id' => $this->reservation->id,
            'url' => route('admin.table-reservations.index'),
        ];
    }
}
