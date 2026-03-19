<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Médecin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Nouveau Médecin</h1>

        <form action="{{ route('medecins.store') }}" method="POST">
            @csrf 

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nom :</label>
                <input type="text" name="nom" required class="w-full border rounded-lg px-4 py-2" value="{{ old('nom') }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Prénom :</label>
                <input type="text" name="prenom" required class="w-full border rounded-lg px-4 py-2" value="{{ old('prenom') }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Téléphone :</label>
                <input type="text" name="telephone" required placeholder="Ex: 06 00 00 00 00" class="w-full border rounded-lg px-4 py-2" value="{{ old('telephone') }}">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Spécialité :</label>
                <select name="specialite_id" class="w-full border rounded-lg px-4 py-2 bg-gray-50">
                    @foreach($specialites as $specialite)
                        <option value="{{ $specialite->id }}">{{ $specialite->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('medecins.index') }}" class="text-gray-500 hover:text-gray-800">Annuler</a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</body>
</html>