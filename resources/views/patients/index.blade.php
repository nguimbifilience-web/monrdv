@extends('layouts.app')

@section('content')
<div style="display: flex; gap: 20px; align-items: flex-start; padding: 10px;">

    <div style="flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 20px; color: #10b981; border-bottom: 2px solid #10b981; padding-bottom: 5px;">+ Nouveau Patient</h2>
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 10px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Nom</label>
                <input type="text" name="nom" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Prénom</label>
                <input type="text" name="prenom" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Téléphone</label>
                <input type="text" name="telephone" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Email (Optionnel)</label>
                <input type="email" name="email" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <button type="submit" style="width: 100%; background: #10b981; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Enregistrer le Patient</button>
        </form>
    </div>

    <div style="flex: 2; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 15px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between;">
            <h2 style="font-size: 18px; font-weight: bold;">👥 Liste des Patients (A-Z)</h2>
        </div>
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 12px; border-bottom: 1px solid #ddd;">Patient</th>
                    <th style="padding: 12px; border-bottom: 1px solid #ddd;">Téléphone</th>
                    <th style="padding: 12px; border-bottom: 1px solid #ddd; text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><strong>{{ $patient->nom_complet }}</strong></td>
                    <td style="padding: 12px;">{{ $patient->telephone }}</td>
                    <td style="padding: 12px; text-align:center;">
                        <a href="{{ route('patients.show', $patient->id) }}" style="color: #3b82f6;">Voir Dossier</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="padding:20px; text-align:center;">Aucun patient enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection