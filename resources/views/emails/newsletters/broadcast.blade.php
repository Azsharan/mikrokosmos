@component('mail::message')
# {{ $newsletter->title }}

{!! nl2br(e($newsletter->content)) !!}

{{ __('Gracias por ser parte de :app', ['app' => config('app.name')]) }}
@endcomponent
