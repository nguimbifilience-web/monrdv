<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MonRDV - Liste des Médecins</title>
    <script src="https://cdn.tailwindcss.com"></script> </head>
<body class="bg-gray-100 p-8">

    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Tableau de Bord Médical</h1>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-500 text-white p-6 rounded-lg shadow">
                <h3 class="font-bold">Total Médecins</h3>
                <p class="text-2xl">{{ $stats['total_medecins'] ?? 0 }}</p>
            </div>
            <div class="bg-green-500 text-white p-6 rounded-lg shadow">
                <h3 class="font-bold">Total Patients</h3>
                <p class="text-2xl">{{ $stats['total_patients'] ?? 0 }}</p>
            </div>
            <div class="bg-purple-500 text-white p-6 rounded-lg shadow">
                <h3 class="font-bold">Spécialités</h3>
                <p class="text-2xl">{{ $stats['total_specialites'] ?? 0 }}</p>
            </div>
        </div>

        <div class="mb-4">
            <a href="{{ route('rendezvous.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">
                + Nouveau Rendez-vous
            </a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">Nom</th>
                        <th class="px-6 py-3 text-left">Prénom</th>
                        <th class="px-6 py-3 text-left">Spécialité</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medecins as $medecin)
                    <tr class="border-t">
                        <td class="px-6 py-4">{{ $medecin->nom }}</td>
                        <td class="px-6 py-4">{{ $medecin->prenom }}</td>
                        <td class="px-6 py-4">{{ $medecin->specialite->nom ?? 'Généraliste' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center">Aucun médecin trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>