@component('mail::message')
# {{ __('Actualización sobre tu reserva en :app', ['app' => config('app.name')]) }}

{{ __('Hola :name,', ['name' => $reservation->name]) }}

{{ __('Queríamos informarte que la reserva **#:code** del producto **:product** ha sido cancelada.', [
    'code' => $reservation->code,
    'product' => $product->name,
]) }}

{{ __('Si aún estás interesado en el artículo, contáctanos para verificar disponibilidad o crear una nueva reserva.') }}

{{ __('Gracias por tu comprensión.') }}  
{{ config('app.name') }}
@endcomponent
