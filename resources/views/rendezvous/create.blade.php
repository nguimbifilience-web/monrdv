@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 20px; font-size: 20px; font-weight: bold;">Nouveau Rendez-vous</h2>

    <form action="{{ route('rendezvous.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Patient</label>
            <select name="patient_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->nom_complet }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Médecin</label>
            <select name="medecin_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                @foreach($medecins as $medecin)
                    <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }} ({{ $medecin->specialite->nom }})</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Date et Heure</label>
            <input type="datetime-local" name="date_rdv" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Motif</label>
            <textarea name="motif" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
        </div>

        <button type="submit" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%;">
            Confirmer le RDV
        </button>
    </form>
</div>
@endsection