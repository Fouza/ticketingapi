@component('mail::message')
    Notification d'un Ticket Effectué !

{{ $mailData['title'] }}
<br>

{{ $mailData['body'] }}

<br>
{{ $mailData['sub'] }}

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

Merci,<br>
{{ config('app.name') }}
@endcomponent
