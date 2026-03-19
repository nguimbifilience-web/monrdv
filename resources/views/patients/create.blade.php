@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 25px; font-size: 22px; font-weight: bold; color: #1f2937; border-bottom: 2px solid #10b981; padding-bottom: 10px;">
        🆕 Nouveau Dossier Patient
    </h2>

    <form action="{{ route('patients.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Nom</label>
                <input type="text" name="nom" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;" placeholder="DURAND" required>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Prénom</label>
                <input type="text" name="prenom" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;" placeholder="Jean" required>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Téléphone</label>
            <input type="text" name="telephone" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;" placeholder="06 00 00 00 00" required>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">E-mail (Optionnel)</label>
            <input type="email" name="email" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;" placeholder="exemple@mail.com">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Date de Naissance</label>
            <input type="date" name="date_naissance" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="flex: 2; background: #10b981; color: white; padding: 12px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">
                Enregistrer le Patient
            </button>
            <a href="{{ route('patients.index') }}" style="flex: 1; text-align: center; background: #f3f4f6; color: #4b5563; padding: 12px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection