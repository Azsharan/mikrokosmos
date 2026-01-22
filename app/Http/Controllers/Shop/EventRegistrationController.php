<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;

class EventRegistrationController extends Controller
{
    public function store(Event $event): RedirectResponse
    {
        $shopUser = auth('shop')->user();

        if (! $shopUser) {
            return redirect()->guest(route('shop.login'));
        }

        abort_unless($event->is_published, 404);

        if ($event->isFull()) {
            return back()->withErrors([
                'event' => __('Este evento alcanzó el cupo máximo.'),
            ], 'eventRegistration');
        }

        $alreadyRegistered = $event->registrations()
            ->where('shop_user_id', $shopUser->id)
            ->exists();

        if ($alreadyRegistered) {
            return back()->withErrors([
                'event' => __('Ya estás registrado en este evento.'),
            ], 'eventRegistration');
        }

        $event->registrations()->create([
            'shop_user_id' => $shopUser->id,
            'status' => 'confirmed',
        ]);

        return back()->with('event_registration_status', __('Registro confirmado. Nos vemos en el evento!'));
    }
}
