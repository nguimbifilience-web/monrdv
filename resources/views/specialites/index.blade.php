@extends('layouts.app')

@section('content')
<div style="display: flex; gap: 20px; align-items: flex-start; padding: 10px;">

    <div style="flex: 1; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 20px; color: #6366f1; border-bottom: 2px solid #6366f1; padding-bottom: 5px;">+ Spécialité</h2>
        <form action="{{ route('specialites.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-size:12px; font-weight:bold;">Nom de la Spécialité</label>
                <input type="text" name="nom" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;" placeholder="ex: Cardiologie" required>
            </div>
            <button type="submit" style="width: 100%; background: #6366f1; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Ajouter au Catalogue</button>
        </form>
    </div>

    <div style="flex: 2; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="padding: 15px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <h2 style="font-size: 18px; font-weight: bold;">🏥 Services Médicaux</h2>
        </div>
        <table style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 12px;">Spécialité</th>
                    <th style="padding: 12px; text-align:center;">Nombre de Médecins</th>
                </tr>
            </thead>
            <tbody>
                @forelse($specialites as $s)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight:bold;">{{ $s->nom }}</td>
                    <td style="padding: 12px; text-align:center;">{{ $s->medecins_count ?? 0 }}</td>
                </tr>
                @empty
                <tr><td colspan="2" style="padding:20px; text-align:center;">Aucune spécialité définie.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection