@component('mail::message')
# {{ $heading }},

{!! $content !!}

@component('mail::button', ['url' => $plans])
PLANS
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent