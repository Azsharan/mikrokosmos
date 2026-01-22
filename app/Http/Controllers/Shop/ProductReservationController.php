<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReservationController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'quantity' => ['required', 'integer', 'min:1', 'max:10'],
                'notes' => ['nullable', 'string', 'max:2000'],
            ],
            [],
            [
                'name' => __('Nombre'),
                'email' => __('Correo electrónico'),
                'phone' => __('Teléfono'),
                'quantity' => __('Cantidad'),
                'notes' => __('Notas'),
            ]
        );

        $product->reservations()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with('reservation_status', __('Gracias por tu interés. Nuestro equipo se pondrá en contacto contigo para confirmar la reserva.'));
    }
}
