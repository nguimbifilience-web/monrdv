<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordonnance - {{ $ordonnance->patient->nom }}</title>
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
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { text-align: left; padding: 8px 6px; border-bottom: 2px solid #111; font-size: 11px; text-transform: uppercase; }
        td { padding: 10px 6px; border-bottom: 1px solid #eee; vertical-align: top; }
        .notes { margin-top: 20px; font-style: italic; color: #333; }
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
            <h1 class="clinic-name">{{ $ordonnance->clinic->name ?? config('app.name') }}</h1>
            @if($ordonnance->clinic)
                <div class="clinic-info">{{ $ordonnance->clinic->address ?? '' }}</div>
                <div class="clinic-info">Tel : {{ $ordonnance->clinic->phone ?? '' }} &nbsp; {{ $ordonnance->clinic->email ?? '' }}</div>
            @endif
        </div>
        <div class="medecin">
            <strong>Dr {{ $ordonnance->medecin->nom }} {{ $ordonnance->medecin->prenom }}</strong>
            <span>{{ optional($ordonnance->medecin->specialite)->nom }}</span>
        </div>
    </div>

    <h2 class="title">Ordonnance</h2>

    <div class="patient-block">
        <span>Patient :</span> {{ $ordonnance->patient->nom }} {{ $ordonnance->patient->prenom }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span>Date :</span> {{ $ordonnance->date->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:40%">Medicament</th>
                <th style="width:30%">Posologie</th>
                <th style="width:15%">Duree</th>
                <th style="width:15%">Quantite</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordonnance->lignes as $l)
                <tr>
                    <td><strong>{{ $l->medicament }}</strong></td>
                    <td>{{ $l->posologie }}</td>
                    <td>{{ $l->duree }}</td>
                    <td>{{ $l->quantite }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($ordonnance->notes_generales)
        <p class="notes">{{ $ordonnance->notes_generales }}</p>
    @endif

    <div class="signature">
        <div class="signature-line">Signature et cachet du medecin</div>
    </div>
</body>
</html>
