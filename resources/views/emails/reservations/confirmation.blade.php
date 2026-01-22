@component('mail::message')
# {{ __('Gracias por tu reserva en :app', ['app' => config('app.name')]) }}

{{ __('Hola :name,', ['name' => $reservation->name]) }}

{{ __('Hemos recibido tu solicitud para apartar **:product**.', ['product' => $product->name]) }}

@component('mail::panel')
**{{ __('Código de reserva') }}:** {{ $reservation->code }}  
**{{ __('Cantidad solicitada') }}:** {{ $reservation->quantity }}
@endcomponent

@if($reservation->notes)
{{ __('Notas que nos compartiste: :notes', ['notes' => $reservation->notes]) }}
@endif

{{ __('Nos pondremos en contacto contigo para coordinar la entrega o recogida. Si tienes alguna duda, responde a este correo o llámanos directamente.') }}

> {{ __('Recuerda que reservaremos este producto para ti durante 5 días laborables. Después de ese plazo la unidad volverá a estar disponible en tienda.') }}

{{ __('¡Gracias por confiar en nosotros!') }}  
{{ config('app.name') }}
@endcomponent
