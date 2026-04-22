@component('mail::message')
    # Votre compte a été activé

    Bonjour **{{ $userName }}**,

    Votre compte a été **activé** par un administrateur. Vous pouvez désormais accéder à la plateforme.

    @component('mail::panel')
        **Organisation :** {{ $organisation }}
        **Rôle :** {{ $role }}
        **Province :** {{ $province }}
    @endcomponent

    @component('mail::button', ['url' => $loginUrl])
        Se connecter
    @endcomponent

    Si vous n’êtes pas à l’origine de cette demande, veuillez contacter un administrateur.

    Merci,
    **Équipe RFD**
@endcomponent
