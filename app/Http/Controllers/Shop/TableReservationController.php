<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\TableReservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TableReservationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user('shop');

        $upcomingReservations = $user->tableReservations()
            ->latest('reserved_for')
            ->get();

        $scheduledBlocks = TableReservation::query()
            ->where('reserved_for', '>=', now())
            ->where('reserved_for', '<=', now()->copy()->addWeek())
            ->orderBy('reserved_for')
            ->get()
            ->groupBy(fn (TableReservation $reservation) => $reservation->reserved_for->toDateString());

        return view('shop.table-reservations', [
            'user' => $user,
            'upcomingReservations' => $upcomingReservations,
            'scheduledBlocks' => $scheduledBlocks,
            'sessionDurationMinutes' => TableReservation::SESSION_DURATION_MINUTES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user('shop');

        $validated = $request->validate(
            [
                'table_number' => [
                    'required',
                    'integer',
                    Rule::in(range(1, TableReservation::TOTAL_TABLES)),
                ],
                'reserved_for' => ['required', 'date', 'after:now'],
                'party_size' => ['required', 'integer', 'min:2', 'max:'.TableReservation::MAX_PARTY_SIZE],
                'notes' => ['nullable', 'string', 'max:2000'],
            ],
            [],
            [
                'table_number' => __('Mesa'),
                'reserved_for' => __('Fecha y hora'),
                'party_size' => __('Número de jugadores'),
                'notes' => __('Notas'),
            ]
        );

        $start = Carbon::parse($validated['reserved_for'])->seconds(0);
        $end = (clone $start)->addMinutes(TableReservation::SESSION_DURATION_MINUTES);

        if ($start->lt(now()->addMinutes(30))) {
            return back()
                ->withErrors(['reserved_for' => __('Las reservas deben hacerse con al menos 30 minutos de anticipación.')])
                ->withInput();
        }

        $occupiedSeats = TableReservation::query()
            ->where('table_number', $validated['table_number'])
            ->where('status', '!=', TableReservation::STATUS_CANCELLED)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('reserved_for', [$start, $end])
                    ->orWhereBetween('reserved_until', [$start, $end])
                    ->orWhere(function ($subQuery) use ($start, $end) {
                        $subQuery
                            ->where('reserved_for', '<=', $start)
                            ->where('reserved_until', '>=', $end);
                    });
            })
            ->sum('party_size');

        $remainingSeats = TableReservation::MAX_PARTY_SIZE - $occupiedSeats;

        if ($remainingSeats < $validated['party_size']) {
            return back()
                ->withErrors(['reserved_for' => __('Solo hay :seats asientos disponibles para esa mesa y horario.', ['seats' => max(0, $remainingSeats)])])
                ->withInput();
        }

        $userConflict = $user->tableReservations()
            ->where('status', '!=', TableReservation::STATUS_CANCELLED)
            ->where('reserved_until', '>', $start)
            ->where('reserved_for', '<', $end)
            ->exists();

        if ($userConflict) {
            return back()
                ->withErrors(['reserved_for' => __('Ya tienes una reserva en ese horario. Cancela la anterior para agendar una nueva.')])
                ->withInput();
        }

        $reservation = $user->tableReservations()->create([
            'table_number' => $validated['table_number'],
            'party_size' => $validated['party_size'],
            'reserved_for' => $start,
            'reserved_until' => $end,
            'status' => TableReservation::STATUS_PENDING,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('shop.tables.index')
            ->with('table_reservation_status', __('Tu mesa quedó apartada. Recibirás confirmación en tienda al llegar. Código: :code', [
                'code' => $reservation->code,
            ]));
    }

    public function destroy(Request $request, TableReservation $tableReservation): RedirectResponse
    {
        $user = $request->user('shop');

        abort_unless($tableReservation->shop_user_id === $user->id, 403);

        if ($tableReservation->status !== TableReservation::STATUS_CANCELLED) {
            $tableReservation->forceFill(['status' => TableReservation::STATUS_CANCELLED])->save();
        }

        return redirect()
            ->route('shop.tables.index')
            ->with('table_reservation_status', __('Tu reserva fue cancelada.'));
    }
}
