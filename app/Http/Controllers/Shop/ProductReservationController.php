<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\ReservationConfirmationMail;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\User;
use App\Notifications\NewReservationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ProductReservationController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $shopUser = auth('shop')->user();

        $validated = $request->validate(
            $this->reservationRules($shopUser),
            [],
            $this->reservationAttributes($shopUser)
        );

        $reservation = $product->reservations()->create([
            'name' => $shopUser?->name ?? $validated['name'],
            'email' => $shopUser?->email ?? $validated['email'],
            'phone' => $shopUser?->phone ?? ($validated['phone'] ?? null),
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        if ($this->shouldSendReservationConfirmation()) {
            try {
                Mail::to($reservation->email)->send(new ReservationConfirmationMail($reservation));
            } catch (Throwable $exception) {
                Log::warning('Failed to send reservation confirmation email', [
                    'reservation_id' => $reservation->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $this->notifyAdmins($reservation);

        return back()->with('reservation_status', __('Gracias por tu interés. Nuestro equipo se pondrá en contacto contigo para confirmar la reserva.'));
    }

    protected function reservationRules($shopUser = null): array
    {
        $rules = [
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        if (! $shopUser) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:50'];
        }

        return $rules;
    }

    protected function reservationAttributes($shopUser = null): array
    {
        $attributes = [
            'quantity' => __('Cantidad'),
            'notes' => __('Notas'),
        ];

        if (! $shopUser) {
            $attributes['name'] = __('Nombre');
            $attributes['email'] = __('Correo electrónico');
            $attributes['phone'] = __('Teléfono');
        }

        return $attributes;
    }

    protected function shouldSendReservationConfirmation(): bool
    {
        $defaultMailer = config('mail.default');

        if (! $defaultMailer) {
            return false;
        }

        $mailerConfig = config("mail.mailers.{$defaultMailer}");

        return is_array($mailerConfig) && ! empty($mailerConfig);
    }

    protected function notifyAdmins(Reservation $reservation): void
    {
        $admins = User::query()->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new NewReservationNotification($reservation));
    }
}
