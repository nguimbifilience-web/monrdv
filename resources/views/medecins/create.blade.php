<h1>Ajouter un nouveau Médecin</h1>

<form action="{{ route('medecins.store') }}" method="POST">
    @csrf
    <div>
        <label>Nom :</label>
        <input type="text" name="nom" required>
    </div>
    <div>
        <label>Prénom :</label>
        <input type="text" name="prenom" required>
    </div>
    <div>
        <label>Spécialité :</label>
        <select name="specialite_id">
            @foreach($specialites as $specialite)
                <option value="{{ $specialite->id }}">{{ $specialite->nom }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit">Enregistrer le médecin</button>
</form>