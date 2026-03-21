<div class="container">
    <h1>Espace Administration : Gestion des Médecins</h1>
    
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    <a href="{{ route('medecins.create') }}" style="background: blue; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
        + Ajouter un nouveau médecin
    </a>

    <table border="1" style="width:100%; margin-top: 20px; border-collapse: collapse;">
        <thead style="background: #eee;">
            <tr>
                <th>Nom Complet</th>
                <th>Spécialité</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($medecins as $medecin)
                <tr>
                    <td>Dr. {{ $medecin->nom }} {{ $medecin->prenom }}</td>
                    <td>{{ $medecin->specialite->nom ?? 'N/A' }}</td>
                    <td>{{ $medecin->email ?? 'Non renseigné' }}</td>
                    <td>
                        <button>Modifier</button>
                        <button style="color: red;">Supprimer</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">
                        Aucun médecin dans la base. Utilisez le bouton "Ajouter".
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>