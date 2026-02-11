@component('mail::message')
# Compte Activé

Bonjour {{ $user->name }},

Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter à l'application.

@component('mail::button', ['url' => route('login')])
Se connecter
@endcomponent

Cordialement,<br>
{{ config('app.name') }}
@endcomponent
