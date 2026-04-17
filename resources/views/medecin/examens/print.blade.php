<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Feuille d'examen - {{ $feuille->patient->nom }}</title>
    <style>
        @page { size: A4; margin: 2cm; }
        body { font-family: 'Georgia', serif; color: #111; font-size: 13px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; }
        .clinic-name { font-size: 22px; font-weight: bold; color: #2563eb; margin: 0; }
        .clinic-info { font-size: 11px; color: #666; margin: 4px 0; }
        .medecin { font-size: 12px; text-align: right; }
        .medecin strong { display: block; font-size: 14px; color: #111; }
        .title { text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 4px; margin: 20px 0; text-transform: uppercase; }
        .patient-block { background: #f3f4f6; padding: 10px 14px; border-radius: 6px; margin-bottom: 18px; }
        .patient-block span { font-weight: bold; }
        .motif { margin-bottom: 18px; padding: 10px 14px; border-left: 4px solid #2563eb; background: #eff6ff; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { text-align: left; padding: 8px 6px; border-bottom: 2px solid #111; font-size: 11px; text-transform: uppercase; }
        td { padding: 10px 6px; border-bottom: 1px solid #eee; vertical-align: top; }
        .urgent { background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; }
        .signature { margin-top: 60px; text-align: right; }
        .signature-line { display: inline-block; width: 250px; border-top: 1px solid #111; padding-top: 6px; font-size: 11px; }
        .print-btn { position: fixed; top: 20px; right: 20px; padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Imprimer</button>

    <div class="header">
        <div>
            <h1 class="clinic-name">{{ $feuille->clinic->name ?? config('app.name') }}</h1>
            @if($feuille->clinic)
                <div class="clinic-info">{{ $feuille->clinic->address ?? '' }}</div>
                <div class="clinic-info">Tel : {{ $feuille->clinic->phone ?? '' }} &nbsp; {{ $feuille->clinic->email ?? '' }}</div>
            @endif
        </div>
        <div class="medecin">
            <strong>Dr {{ $feuille->medecin->nom }} {{ $feuille->medecin->prenom }}</strong>
            <span>{{ optional($feuille->medecin->specialite)->nom }}</span>
        </div>
    </div>

    <h2 class="title">Demande d'examens</h2>

    <div class="patient-block">
        <span>Patient :</span> {{ $feuille->patient->nom }} {{ $feuille->patient->prenom }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span>Date :</span> {{ $feuille->date->format('d/m/Y') }}
    </div>

    @if($feuille->motif_clinique)
        <div class="motif"><strong>Motif clinique :</strong> {{ $feuille->motif_clinique }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:20%">Type</th>
                <th style="width:65%">Examen demande</th>
                <th style="width:15%">Urgence</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feuille->lignes as $l)
                <tr>
                    <td style="text-transform:uppercase">{{ $l->type_examen }}</td>
                    <td><strong>{{ $l->libelle }}</strong></td>
                    <td>@if($l->urgence)<span class="urgent">URGENT</span>@endif</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <div class="signature-line">Signature et cachet du medecin</div>
    </div>
</body>
</html>
