@extends('layouts.app')

@section('content')
<div style="display: flex; gap: 20px; align-items: flex-start; padding: 10px;">

    <div style="flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 20px; color: #3b82f6; border-bottom: 2px solid #3b82f6; padding-bottom: 5px;">🗓️ Fixer un RDV</h2>
        <form action="{{ route('rendezvous.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 10px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Patient</label>
                <select name="patient_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                    @foreach($patients as $p) <option value="{{ $p->id }}">{{ $p->nom_complet }}</option> @endforeach
                </select>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Médecin</label>
                <select name="medecin_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                    @foreach($medecins as $m) <option value="{{ $m->id }}">Dr. {{ $m->nom }} ({{ $m->specialite->nom }})</option> @endforeach
                </select>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Date & Heure</label>
                <input type="datetime-local" name="date_rdv" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
            </div>
            <button type="submit" style="width: 100%; background: #3b82f6; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Valider le RDV</button>
        </form>
    </div>

    <div style="flex: 2; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 15px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-size: 18px; font-weight: bold;">📅 Planning Chronologique</h2>
        </div>
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 12px;">Date</th>
                    <th style="padding: 12px;">Patient</th>
                    <th style="padding: 12px;">Médecin</th>
                    <th style="padding: 12px; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rdvs as $rdv)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; color: #3b82f6; font-weight: bold;">{{ \Carbon\Carbon::parse($rdv->date_rdv)->format('d/m/H:i') }}</td>
                    <td style="padding: 12px;">{{ $rdv->patient->nom_complet }}</td>
                    <td style="padding: 12px;">Dr. {{ $rdv->medecin->nom }}</td>
                    <td style="padding: 12px; text-align:center;"><a href="{{ route('rendezvous.show', $rdv->id) }}" style="color: #64748b;">Voir</a></td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:20px; text-align:center;">Aucun RDV prévu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection