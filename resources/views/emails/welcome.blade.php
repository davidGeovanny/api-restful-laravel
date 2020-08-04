@component('mail::message')
# Hola {{$user->name}}

Gracias por crear una cuenta. Por favor verifícala usando el siguiente botón:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Confirmar mi cuenta
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent