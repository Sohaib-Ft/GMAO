@component('mail::message')
# Compte Désactivé

Bonjour {{ $user->name }},

Votre compte a été désactivé par un administrateur. Vous ne pouvez plus accéder à l'application.

Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur.

Cordialement,<br>
{{ config('app.name') }}
@endcomponent
