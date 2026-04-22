<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Activation du compte</title>
</head>

<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:12px;overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#111827;padding:20px;text-align:center;">
                            <img src="{{ asset('images/logo/logo-white.png') }}" alt="AlertBook" style="height:40px;">
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;color:#111827;font-size:15px;line-height:1.6;">

                            <p>Bonjour <strong>{{ $userName }}</strong>,</p>

                            <p>
                                Votre compte a été <strong>activé</strong> avec succès par un administrateur.
                                Vous pouvez désormais accéder à la plateforme.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin:20px 0;background:#f9fafb;border-radius:8px;padding:15px;">
                                <tr>
                                    <td style="font-size:14px;">
                                        <strong>Organisation :</strong> {{ $organisation }}<br>
                                        <strong>Rôle :</strong> {{ $role }}<br>
                                        <strong>Province :</strong> {{ $province }}
                                    </td>
                                </tr>
                            </table>

                            <p style="text-align:center;margin:30px 0;">
                                <a href="{{ $loginUrl }}"
                                    style="background:#111827;color:#ffffff;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:bold;">
                                    Se connecter à la plateforme
                                </a>
                            </p>

                            <p style="font-size:13px;color:#6b7280;">
                                Si vous n’êtes pas à l’origine de cette demande, veuillez contacter votre
                                administrateur.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9fafb;padding:15px;text-align:center;font-size:12px;color:#9ca3af;">
                            © {{ date('Y') }} AlertBook — Développé par Research For Development (RFD)
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
