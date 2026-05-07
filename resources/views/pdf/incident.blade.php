<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche d'Alerte Humanitaire - {{ $incident->code_incident }}</title>
    <style>
        @page {
            margin: 0;
        }
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
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-box {
            width: 60px;
        }
        .logo {
            height: 50px;
            background: white;
            padding: 5px;
            border-radius: 4px;
        }
        .header-title {
            padding-left: 20px;
        }
        .header-title h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-title p {
            margin: 0;
            font-size: 12px;
            opacity: 0.9;
        }
        .container {
            padding: 30px 40px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-info { background-color: #e0f2fe; color: #075985; }

        .section {
            margin-bottom: 25px;
        }
        .section-header {
            border-bottom: 2px solid #0B4F8A;
            margin-bottom: 12px;
            padding-bottom: 4px;
        }
        .section-header h2 {
            margin: 0;
            font-size: 14px;
            color: #0B4F8A;
            text-transform: uppercase;
        }
        
        .highlight-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .event-highlight {
            font-size: 18px;
            font-weight: bold;
            color: #b91c1c;
            margin-bottom: 10px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table td {
            padding: 6px 0;
            vertical-align: top;
        }
        .label {
            color: #6b7280;
            font-weight: bold;
            width: 150px;
        }
        .value {
            font-weight: 500;
        }

        .grid-2 {
            display: table;
            width: 100%;
        }
        .col {
            display: table-cell;
            width: 50%;
        }

        .description-box {
            background-color: #fff;
            border-left: 4px solid #0B4F8A;
            padding: 10px 15px;
            font-style: italic;
        }

        table.list-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.list-table th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.list-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
        }

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
        .map-box {
            text-align: center;
            margin-top: 10px;
        }
        .map-image {
            width: 100%;
            max-height: 250px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <table class="header-table">
            <tr>
                <td class="logo-box">
                    @php
                        $logoPath = public_path('images/logo/logo-main.png');
                        if (!file_exists($logoPath)) {
                            $logoPath = public_path('images/logo/logo-main_.png');
                        }
                    @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" class="logo">
                    @else
                        <div style="background: white; color: #0B4F8A; padding: 10px; font-weight: bold; border-radius: 4px;">AlertBook</div>
                    @endif
                </td>
                <td class="header-title">
                    <h1>Fiche d'Alerte Humanitaire</h1>
                    <p>Système de Veille et d'Alerte en Temps Réel | RDC</p>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 18px; font-weight: bold;">#{{ $incident->code_incident }}</div>
                    <div style="font-size: 11px;">Généré le : {{ $generatedAt->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        
        {{-- Résumé de l'alerte --}}
        <div class="highlight-box">
            <div class="event-highlight">
                TYPE D'ÉVÉNEMENT : {{ strtoupper($incident->evenement->nom_evenement ?? 'Inconnu') }}
            </div>
            <div class="grid-2">
                <div class="col">
                    <table class="data-table">
                        <tr>
                            <td class="label">Date de l'incident :</td>
                            <td class="value">{{ optional($incident->date_incident)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Sévérité :</td>
                            <td class="value">
                                @php
                                    $sevClass = match($incident->severite) {
                                        'Élevée', 'Critique' => 'badge-danger',
                                        'Moyenne' => 'badge-warning',
                                        default => 'badge-info'
                                    };
                                @endphp
                                <span class="badge {{ $sevClass }}">{{ $incident->severite ?? 'N/A' }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col">
                    <table class="data-table">
                        <tr>
                            <td class="label">Statut :</td>
                            <td class="value">
                                @php
                                    $statusClass = match($incident->statut_incident) {
                                        'Validé' => 'badge-success',
                                        'Archivé' => 'badge-danger',
                                        default => 'badge-warning'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $incident->statut_incident }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Assigné à :</td>
                            <td class="value">{{ $incident->assignedTo->name ?? 'Non assigné' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Section I: Localisation --}}
        <div class="section">
            <div class="section-header">
                <h2>I. Localisation Géographique</h2>
            </div>
            <div class="grid-2">
                <div class="col" style="padding-right: 20px;">
                    <table class="data-table">
                        <tr><td class="label">Province :</td><td class="value">{{ $incident->province->nom_province ?? '-' }}</td></tr>
                        <tr><td class="label">Territoire :</td><td class="value">{{ $incident->territoire->nom_territoire ?? '-' }}</td></tr>
                        <tr><td class="label">Chefferie :</td><td class="value">{{ $incident->chefferie->nom_chefferie ?? '-' }}</td></tr>
                        <tr><td class="label">Zone de Santé :</td><td class="value">{{ $incident->zoneSante->nom_zonesante ?? '-' }}</td></tr>
                        <tr><td class="label">Aire de Santé :</td><td class="value">{{ $incident->aireSante->nom_airesante ?? '-' }}</td></tr>
                        <tr><td class="label">Localité :</td><td class="value">{{ $incident->localite ?? '-' }}</td></tr>
                        @if($incident->latitude && $incident->longitude)
                        <tr><td class="label">Coordonnées :</td><td class="value">{{ $incident->latitude }}, {{ $incident->longitude }}</td></tr>
                        @endif
                    </table>
                </div>
                <div class="col">
                    @if($mapBase64)
                    <div class="map-box">
                        <img src="{{ $mapBase64 }}" class="map-image">
                        <div style="font-size: 8px; color: #9ca3af; margin-top: 4px;">Source: OpenStreetMap via Yandex</div>
                    </div>
                    @else
                    <div style="background-color: #f3f4f6; border-radius: 8px; height: 120px; display: table; width: 100%;">
                        <div style="display: table-cell; vertical-align: middle; text-align: center; color: #9ca3af;">
                            @if($incident->latitude && $incident->longitude)
                                Erreur de chargement de la carte
                            @else
                                Coordonnées GPS non disponibles
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Section II: Description --}}
        <div class="section">
            <div class="section-header">
                <h2>II. Description des Faits</h2>
            </div>
            <div class="description-box">
                {!! nl2br(e($incident->description_faits)) !!}
            </div>
            <table class="data-table" style="margin-top: 10px;">
                <tr><td class="label">Source info :</td><td class="value">{{ $incident->source_info ?? 'N/A' }}</td></tr>
                <tr><td class="label">Auteur présumé :</td><td class="value">{{ $incident->auteur_presume ?? 'Inconnu' }}</td></tr>
            </table>
        </div>

        {{-- Section III: Impact et Violences --}}
        <div class="section">
            <div class="section-header">
                <h2>III. Impact et Violences Signalées</h2>
            </div>
            @if($incident->violences->count() > 0)
                <table class="list-table">
                    <thead>
                        <tr>
                            <th width="30%">Catégorie</th>
                            <th width="30%">Type de Violence</th>
                            <th>Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incident->violences as $v)
                        <tr>
                            <td>{{ $v->categorie_name }}</td>
                            <td><strong>{{ $v->violence_name }}</strong></td>
                            <td>{{ $v->pivot->description_violence ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Aucune violence spécifique n'a été documentée.</p>
            @endif
        </div>

        {{-- Section IV: Mouvements de Population --}}
        @if($incident->mouvements && $incident->mouvements->count() > 0)
        <div class="section">
            <div class="section-header">
                <h2>IV. Mouvements de Population</h2>
            </div>
            <table class="list-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Provenance</th>
                        <th>Accueil</th>
                        <th>Estim. Ménages</th>
                        <th>Estim. Pers.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incident->mouvements as $m)
                    <tr>
                        <td>{{ optional($m->date_mouvement)->format('d/m/Y') }}</td>
                        <td>{{ $m->type_mouvement }}</td>
                        <td>{{ $m->localite_prov ?? '-' }} ({{ $m->territoireProv->nom_territoire ?? '-' }})</td>
                        <td>{{ $m->localite_accl ?? '-' }} ({{ $m->territoireAccl->nom_territoire ?? '-' }})</td>
                        <td style="text-align: right;">{{ number_format($m->estim_nbre_menages) }}</td>
                        <td style="text-align: right;">{{ number_format($m->estim_nbre_personnes) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Section V: Référencements --}}
        @if($incident->referencements && $incident->referencements->count() > 0)
        <div class="section">
            <div class="section-header">
                <h2>V. Réponses et Référencements</h2>
            </div>
            <table class="list-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Structure / Partenaire</th>
                        <th>Type de Service</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incident->referencements as $r)
                    <tr>
                        <td>{{ optional($r->date_referencement)->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $r->provider->provider_name ?? 'N/A' }}</strong>
                            <div style="font-size: 8px; color: #6b7280;">Contact: {{ $r->provider->focalpoint_name ?? '-' }}</div>
                        </td>
                        <td>{{ $r->type_reponse ?? '-' }}</td>
                        <td>{{ $r->statut_reponse ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>

    <div class="footer">
        <table width="100%">
            <tr>
                <td>Document généré par AlertBook Platform | Confidentiel</td>
                <td style="text-align: center;">Page 1 sur 1</td>
                <td style="text-align: right;">Généré par : {{ $generatedBy->name }} ({{ $generatedBy->email }})</td>
            </tr>
        </table>
    </div>

</body>
</html>
