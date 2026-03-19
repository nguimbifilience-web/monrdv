@extends('layouts.app')

@section('content')
<div style="display: flex; gap: 20px; align-items: flex-start; padding: 10px;">

    <div style="flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 20px; color: #6366f1; border-bottom: 2px solid #6366f1; padding-bottom: 5px;">+ Nouveau Médecin</h2>
        <form action="{{ route('medecins.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 10px;">
                <label style="font-size: 12px; font-weight: bold;">Nom du Docteur</label>
                <input type="text" name="nom" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" placeholder="ex: NDONG" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="font-size: 12px; font-weight: bold;">Spécialité</label>
                <select name="specialite_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" required>
                    @foreach($specialites as $s)
                        <option value="{{ $s->id }}">{{ $s->nom }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="width: 100%; background: #6366f1; color: white; padding: 10px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Enregistrer</button>
        </form>
    </div>

    <div style="flex: 2; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        
        <div style="padding: 15px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 18px; font-weight: bold;">👨‍⚕️ Corps Médical</h2>
            
            <form action="{{ route('medecins.index') }}" method="GET" style="display: flex; gap: 5px;">
                <select name="specialite" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc; font-size: 13px;">
                    <option value="">Toutes les spécialités</option>
                    @foreach($specialites as $s)
                        <option value="{{ $s->id }}" {{ request('specialite') == $s->id ? 'selected' : '' }}>{{ $s->nom }}</option>
                    @endforeach
                </select>
                <button type="submit" style="background: #64748b; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Trier</button>
            </form>
        </div>

        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 12px; border-bottom: 1px solid #ddd;">Nom</th>
                    <th style="padding: 12px; border-bottom: 1px solid #ddd;">Spécialité</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medecins as $m)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight:bold;">Dr. {{ strtoupper($m->nom) }}</td>
                    <td style="padding: 12px;"><span style="background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 10px; font-size: 12px;">{{ $m->specialite->nom }}</span></td>
                </tr>
                @empty
                <tr><td colspan="2" style="padding:20px; text-align:center;">Aucun médecin trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection