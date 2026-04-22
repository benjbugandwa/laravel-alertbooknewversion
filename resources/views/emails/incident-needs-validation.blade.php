<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Incident à valider</title>
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

                            <p style="margin:0 0 14px 0;">Bonjour <strong>{{ $userName }}</strong>,</p>

                            <p style="margin:0 0 18px 0;">
                                Un <strong>nouvel incident</strong> vient d’être enregistré et nécessite votre
                                <strong>validation</strong>.
                            </p>

                            <!-- Badges row -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0 8px 0;">
                                <tr>
                                    <td align="left">
                                        <span
                                            style="display:inline-block;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:bold;
                                            background:#fef3c7;color:#92400e;">
                                            ⏳ {{ $statut }}
                                        </span>
                                        <span
                                            style="display:inline-block;margin-left:8px;padding:6px 10px;border-radius:999px;font-size:12px;font-weight:bold;
                                            background:#e0f2fe;color:#075985;">
                                            ⚠️ Sévérité: {{ $severite }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info card -->
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin:14px 0;background:#f9fafb;border-radius:10px;padding:16px;border:1px solid #e5e7eb;">
                                <tr>
                                    <td style="font-size:14px;line-height:1.8;color:#111827;">
                                        <div style="font-weight:bold;font-size:14px;margin-bottom:8px;">
                                            📌 Détails de l’incident
                                        </div>

                                        <div>🆔 <strong>Code :</strong> {{ $codeIncident }}</div>
                                        <div>📅 <strong>Date :</strong> {{ $dateIncident }}</div>
                                        <div>🗺️ <strong>Province :</strong> {{ $province }}</div>
                                        <div>🏘️ <strong>Territoire :</strong> {{ $territoire }}</div>
                                        <div>🏥 <strong>Zone de santé :</strong> {{ $zoneSante }}</div>
                                        <div>📍 <strong>Localité :</strong> {{ $localite }}</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:12px 0 0 0;font-size:13px;color:#6b7280;">
                                🔒 Par confidentialité, les informations sensibles (survivant, faits détaillés, auteur
                                présumé)
                                ne sont pas incluses dans cet e-mail.
                            </p>

                            <!-- CTA -->
                            <p style="text-align:center;margin:26px 0 8px 0;">
                                <a href="{{ $actionUrl }}"
                                    style="background:#111827;color:#ffffff;padding:12px 22px;border-radius:10px;text-decoration:none;font-weight:bold;display:inline-block;">
                                    Ouvrir la liste des incidents
                                </a>
                            </p>

                            <p style="margin:14px 0 0 0;font-size:12px;color:#9ca3af;text-align:center;">
                                Si ce message vous semble inattendu, contactez votre administrateur.
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
