<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche Incident {{ $incident->code_incident }}</title>
    <style>
        * {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        }

        body {
            font-size: 12px;
            color: #111827;
        }

        .page {
            padding: 18px;
        }

        .header {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-table {
            width: 100%;
        }

        .logo {
            height: 32px;
        }

        .title {
            font-size: 16px;
            font-weight: 700;
        }

        .muted {
            color: #6b7280;
            font-size: 11px;
        }

        .section {
            margin-top: 14px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .kv td {
            padding: 6px 8px;
            vertical-align: top;
        }

        .kv td:first-child {
            width: 34%;
            color: #374151;
            font-weight: 700;
        }

        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            border: 1px solid #e5e7eb;
        }

        .tag-yellow {
            background: #fef3c7;
            border-color: #fde68a;
        }

        .tag-green {
            background: #d1fae5;
            border-color: #a7f3d0;
        }

        .tag-gray {
            background: #f3f4f6;
            border-color: #e5e7eb;
        }

        .tag-red {
            background: #fee2e2;
            border-color: #fecaca;
        }

        .list-table th,
        .list-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
        }

        .list-table th {
            background: #f9fafb;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .footer {
            position: fixed;
            bottom: 18px;
            left: 18px;
            right: 18px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 10px;
            color: #6b7280;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- Header --}}
        <div class="header">
            <table class="header-table">
                <tr>
                    <td>
                        <img src="{{ public_path('images/logo/logo.png') }}" class="logo" alt="AlertBook">
                    </td>
                    <td class="right">
                        <div class="title">Fiche Incident</div>
                        <div class="muted">Code : {{ $incident->code_incident }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Section 1: Détails incident --}}
        <div class="section">
            <div class="section-title">1) Détails de l’incident</div>
            <div class="box">
                @php
                    $status = $incident->statut_incident ?? 'En attente';
                    $statusClass = match ($status) {
                        'Validé' => 'tag-green',
                        'Cloturée' => 'tag-gray',
                        'Archivé' => 'tag-red',
                        default => 'tag-yellow',
                    };
                @endphp

                <table class="kv">
                    <tr>
                        <td>Statut</td>
                        <td><span class="tag {{ $statusClass }}">{{ $status }}</span></td>
                    </tr>
                    <tr>
                        <td>Date incident</td>
                        <td>{{ optional($incident->date_incident)->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td>Sévérité</td>
                        <td>{{ $incident->severite ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Événement</td>
                        <td>{{ $incident->evenement->nom_evenement ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Localisation</td>
                        <td>
                            <strong>Province :</strong> {{ $incident->province->nom_province ?? ($incident->code_province ?? '-') }}<br>
                            <strong>Territoire :</strong> {{ $incident->territoire->nom_territoire ?? ($incident->code_territoire ?? '-') }}<br>
                            <strong>Chefferie :</strong> {{ $incident->chefferie->nom_chefferie ?? ($incident->code_chefferie ?? '-') }}<br>
                            <strong>Groupement :</strong> {{ $incident->groupement->nom_groupement ?? ($incident->code_groupement ?? '-') }}<br>
                            <strong>Zone de santé :</strong> {{ $incident->zoneSante->nom_zonesante ?? ($incident->code_zonesante ?? '-') }}<br>
                            <strong>Aire de santé :</strong> {{ $incident->aireSante->nom_airesante ?? ($incident->code_airesante ?? '-') }}<br>
                            <strong>Localité :</strong> {{ $incident->localite ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Survivant</td>
                        <td>{{ $incident->survivant?->code_survivant ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Assigné à</td>
                        <td>{{ $incident->assignedTo?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Source d’information</td>
                        <td>{{ $incident->source_info ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Auteur présumé</td>
                        <td>{{ $incident->auteur_presume ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Confidentialité</td>
                        <td>{{ $incident->confidentiality_level ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Description des faits</td>
                        <td style="white-space: pre-line;">{{ $incident->description_faits ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Section 2: Violences --}}
        <div class="section">
            <div class="section-title">2) Violences associées</div>
            <div class="box">
                @if (($incident->violences?->count() ?? 0) === 0)
                    <div class="muted">Aucune violence associée.</div>
                @else
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Type</th>
                                <th>Description (optionnel)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incident->violences as $v)
                                <tr>
                                    <td>{{ $v->categorie_name ?? '-' }}</td>
                                    <td>{{ $v->violence_name ?? '-' }}</td>
                                    <td style="white-space: pre-line;">{{ $v->pivot->description_violence ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Section 3: Référencements --}}
        <div class="section">
            <div class="section-title">3) Référencements</div>
            <div class="box">
                @if (($incident->referencements?->count() ?? 0) === 0)
                    <div class="muted">Aucun référencement enregistré.</div>
                @else
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Date</th>
                                <th>Structure</th>
                                <th>Type service</th>
                                <th>Statut réponse</th>
                                <th>Focal point</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incident->referencements as $r)
                                <tr>
                                    <td>{{ $r->code_referencement }}</td>
                                    <td>{{ optional($r->date_referencement)->format('Y-m-d') }}</td>
                                    <td>
                                        {{ $r->provider->provider_name ?? '-' }}
                                        @if ($r->provider?->provider_location)
                                            <div class="muted">
                                                @if(is_array($r->provider->provider_location))
                                                    {{ implode(', ', $r->provider->provider_location) }}
                                                @else
                                                    {{ $r->provider->provider_location }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $r->type_reponse ?? '-' }}</td>
                                    <td>{{ $r->statut_reponse ?? '-' }}</td>
                                    <td>
                                        {{ $r->provider->focalpoint_name ?? '-' }}
                                        @if ($r->provider?->focalpoint_number)
                                            <div class="muted">{{ $r->provider->focalpoint_number }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="footer">
        <table style="width:100%;">
            <tr>
                <td>Généré le {{ $generatedAt->format('Y-m-d H:i') }}</td>
                <td class="right">Par : {{ $generatedBy->name ?? '—' }}</td>
            </tr>
        </table>
    </div>

</body>

</html>
