@component('mail::message')
# {{ $heading }},

{!! $content !!}

@if(@$projectEmail)

Please send your bid to <b style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; color: #3d4852; font-size: 18px; font-weight: bold;">{{ @$projectEmail->name }}</b> and this email <b style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; color: #3d4852; font-size: 18px; font-weight: bold;">{{ @$projectEmail->email }}</b>

@endif

@if($plans)
@component('mail::button', ['url' => $plans])
PLANS
@endcomponent

@endif


Thanks,<br>
{{ config('app.name') }}
@endcomponent