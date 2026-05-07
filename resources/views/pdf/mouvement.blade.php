<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de Mouvement de Population - {{ $mouvement->id }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header-banner {
            background-color: #0B4F8A;
            color: white;
            padding: 20px 40px;
            height: 80px;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .logo {
            height: 50px;
            background: white;
            padding: 5px;
            border-radius: 4px;
        }
        .header-title { padding-left: 20px; }
        .header-title h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header-title p { margin: 0; font-size: 11px; opacity: 0.9; }
        
        .container { padding: 30px 40px; }
        
        .section { margin-bottom: 25px; }
        .section-header {
            border-bottom: 2px solid #0B4F8A;
            margin-bottom: 12px;
            padding-bottom: 4px;
        }
        .section-header h2 { margin: 0; font-size: 14px; color: #0B4F8A; text-transform: uppercase; }
        
        .highlight-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .type-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            background-color: {{ $mouvement->type_mouvement === 'Fuite' ? '#fee2e2' : '#d1fae5' }};
            color: {{ $mouvement->type_mouvement === 'Fuite' ? '#991b1b' : '#065f46' }};
        }

        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table td { padding: 8px 0; vertical-align: top; }
        .label { color: #6b7280; font-weight: bold; width: 180px; }
        .value { font-weight: 500; color: #111827; }

        .grid-2 { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; width: 50%; vertical-align: top; }

        .cause-box {
            background-color: #fff;
            border-left: 4px solid #0B4F8A;
            padding: 12px 15px;
            font-size: 14px;
            font-weight: bold;
            color: #0B4F8A;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        .stat-card {
            display: table-cell;
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 48%;
        }
        .stat-value { font-size: 18px; font-weight: bold; color: #0B4F8A; }
        .stat-label { font-size: 10px; color: #6b7280; text-transform: uppercase; }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f3f4f6;
            color: #6b7280;
            font-size: 9px;
            padding: 10px 40px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <table class="header-table">
            <tr>
                <td width="60">
                    @php
                        $logoPath = public_path('images/logo/logo-main.png');
                        if (!file_exists($logoPath)) $logoPath = public_path('images/logo/logo-main_.png');
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" class="logo">
                    @else
                        <div style="background: white; color: #0B4F8A; padding: 10px; font-weight: bold; border-radius: 4px;">AlertBook</div>
                    @endif
                </td>
                <td class="header-title">
                    <h1>Fiche de Mouvement de Population</h1>
                    <p>Système de Veille et d'Alerte Humanitaire | RDC</p>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 12px; font-weight: bold;">Réf: MV-{{ $mouvement->id }}</div>
                    <div style="font-size: 9px;">Généré le : {{ $generatedAt->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        
        <div class="highlight-box">
            <div class="grid-2">
                <div class="col">
                    <div class="stat-label">Type de mouvement</div>
                    <div class="type-badge">{{ $mouvement->type_mouvement }}</div>
                </div>
                <div class="col text-right" style="text-align: right;">
                    <div class="stat-label">Date effective</div>
                    <div style="font-size: 14px; font-weight: bold;">{{ $mouvement->date_mouvement->format('d F Y') }}</div>
                </div>
            </div>
        </div>

        <div class="cause-box">
            CAUSE DU DÉPLACEMENT : 
            @if($mouvement->incident_id)
                {{ strtoupper($mouvement->incident->evenement->nom_evenement ?? 'Incident ' . $mouvement->incident->code_incident) }}
                <div style="font-size: 10px; font-weight: normal; color: #6b7280; margin-top: 4px;">Lien Alerte: #{{ $mouvement->incident->code_incident }}</div>
            @else
                {{ strtoupper($mouvement->cause_deplacement ?? 'Non spécifiée') }}
            @endif
        </div>

        <div class="section">
            <div class="section-header"><h2>I. Détails des Flux</h2></div>
            <div class="grid-2">
                <div class="col" style="padding-right: 15px;">
                    <h3 style="font-size: 11px; color: #991b1b; text-transform: uppercase;">Provenance</h3>
                    <table class="data-table">
                        <tr><td class="label">Province :</td><td class="value">{{ $mouvement->provinceProv->nom_province ?? '-' }}</td></tr>
                        <tr><td class="label">Territoire :</td><td class="value">{{ $mouvement->territoireProv->nom_territoire ?? '-' }}</td></tr>
                        <tr><td class="label">Zone de Santé :</td><td class="value">{{ $mouvement->zoneSanteProv->nom_zonesante ?? '-' }}</td></tr>
                        <tr><td class="label">Localité :</td><td class="value">{{ $mouvement->localite_prov ?? '-' }}</td></tr>
                    </table>
                </div>
                <div class="col" style="padding-left: 15px; border-left: 1px dashed #e5e7eb;">
                    <h3 style="font-size: 11px; color: #065f46; text-transform: uppercase;">Accueil</h3>
                    <table class="data-table">
                        <tr><td class="label">Province :</td><td class="value">{{ $mouvement->provinceAccl->nom_province ?? '-' }}</td></tr>
                        <tr><td class="label">Territoire :</td><td class="value">{{ $mouvement->territoireAccl->nom_territoire ?? '-' }}</td></tr>
                        <tr><td class="label">Zone de Santé :</td><td class="value">{{ $mouvement->zoneSanteAccl->nom_zonesante ?? '-' }}</td></tr>
                        <tr><td class="label">Localité :</td><td class="value">{{ $mouvement->localite_accl ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header"><h2>II. Estimations de la Population</h2></div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($mouvement->estim_nbre_menages) }}</div>
                    <div class="stat-label">Ménages déplacés</div>
                </div>
                <div style="display: table-cell; width: 4%;"></div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($mouvement->estim_nbre_personnes) }}</div>
                    <div class="stat-label">Individus (estimation)</div>
                </div>
            </div>
            <table class="data-table" style="margin-top: 15px;">
                <tr><td class="label">Type de logement :</td><td class="value">{{ $mouvement->type_logement ?? 'Non renseigné' }}</td></tr>
            </table>
        </div>

        <div class="section">
            <div class="section-header"><h2>III. Contexte et Remarques</h2></div>
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; font-style: italic;">
                {!! nl2br(e($mouvement->remarques_mouvement ?? 'Aucune remarque additionnelle.')) !!}
            </div>
            <table class="data-table" style="margin-top: 15px;">
                <tr><td class="label">Source d'information :</td><td class="value">{{ $mouvement->source_info }}</td></tr>
                <tr><td class="label">Rapporteur :</td><td class="value">{{ $mouvement->creator->name }}</td></tr>
            </table>
        </div>

    </div>

    <div class="footer">
        <table width="100%">
            <tr>
                <td>Document généré par AlertBook | Confidentiel</td>
                <td style="text-align: right;">Généré par : {{ $generatedBy->name }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
