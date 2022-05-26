@component('mail::message')
# {{ $heading }},

{!! $content !!}

@if($plans)
@component('mail::button', ['url' => $plans])
PLANS
@endcomponent

@endif



Thanks,<br>
{{ config('app.name') }}
@endcomponent