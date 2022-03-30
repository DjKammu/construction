@component('mail::message')
# {{ $heading }},

{!! $content !!}


Thanks,<br>
{{ config('app.name') }}
@endcomponent