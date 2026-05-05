<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification d'Incident</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f7f9; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #0B4F8A; color: #ffffff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 300; letter-spacing: 1px; }
        .logo { font-size: 28px; font-weight: bold; margin-bottom: 10px; display: block; }
        .content { padding: 30px; }
        .content h2 { color: #0B4F8A; font-size: 20px; margin-top: 0; border-bottom: 2px solid #e1e8ed; padding-bottom: 10px; }
        .info-grid { display: table; width: 100%; margin-top: 20px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; padding: 10px 0; font-weight: bold; color: #555; width: 160px; border-bottom: 1px solid #f0f0f0; }
        .info-value { display: table-cell; padding: 10px 0; color: #333; border-bottom: 1px solid #f0f0f0; }
        .severity { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .severity-critique { background-color: #ffebee; color: #c62828; }
        .severity-élevée { background-color: #fff3e0; color: #ef6c00; }
        .severity-moyenne { background-color: #e3f2fd; color: #1565c0; }
        .severity-faible { background-color: #f1f8e9; color: #33691e; }
        .footer { background-color: #f8fafc; color: #94a3b8; padding: 20px; text-align: center; font-size: 12px; }
        .btn { display: inline-block; background-color: #0B4F8A; color: #ffffff; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: bold; margin-top: 25px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">AlertBook</span>
            <h1>Alerte Incident Critique</h1>
        </div>
        <div class="content">
            <h2>Détails du rapport</h2>
            <p>Un nouvel incident a été rapporté dans votre zone de supervision. Veuillez trouver ci-dessous les détails préliminaires :</p>
            
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Code Incident :</div>
                    <div class="info-value"><strong>{{ $codeIncident }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date :</div>
                    <div class="info-value">{{ $dateIncident }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Événement :</div>
                    <div class="info-value">{{ $eventType }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Sévérité :</div>
                    <div class="info-value">
                        <span class="severity severity-{{ strtolower($severite) }}">{{ $severite }}</span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Organisation :</div>
                    <div class="info-value">{{ $reportingOrg }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Province :</div>
                    <div class="info-value">{{ $province }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Territoire :</div>
                    <div class="info-value">{{ $territoire }}</div>
                </div>
            </div>

            <p style="margin-top: 30px;">Nous vous recommandons d'analyser ce rapport dans les plus brefs délais sur la plateforme de gestion.</p>
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/incidents" class="btn">Accéder au tableau de bord</a>
            </div>
        </div>
        <div class="footer">
            Ceci est un message automatique généré par le système AlertBook.<br>
            &copy; {{ date('Y') }} AlertBook - Système de Surveillance et de Réponse aux Incidents.
        </div>
    </div>
</body>
</html>
