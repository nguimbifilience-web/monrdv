<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonRDV - Gestion des Médecins</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">

    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Tableau de Bord Médical</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm font-medium uppercase">Médecins</p>
                <p class="text-2xl font-bold">{{ $stats['total_medecins'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                <p class="text-gray-500 text-sm font-medium uppercase">Patients</p>
                <p class="text-2xl font-bold">{{ $stats['total_patients'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500">
                <p class="text-gray-500 text-sm font-medium uppercase">Spécialités</p>
                <p class="text-2xl font-bold">{{ $stats['total_specialites'] }}</p>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Liste du personnel</h2>
            <a href="{{ route('rendezvous.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg transition font-medium">
                + Nouveau RDV
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('medecins.index', ['sort' => 'nom', 'direction' => ($sort == 'nom' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="flex items-center hover:text-indigo-600">
                                Nom
                                @if($sort == 'nom')
                                    <span class="ml-1">{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('medecins.index', ['sort' => 'prenom', 'direction' => ($sort == 'prenom' && $direction == 'asc') ? 'desc' : 'asc']) }}" class="flex items-center hover:text-indigo-600">
                                Prénom
                                @if($sort == 'prenom')
                                    <span class="ml-1">{{ $direction == 'asc' ? '↑' : '↓' }}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Spécialité</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($medecins as $medecin)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $medecin->nom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $medecin->prenom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                    {{ $medecin->specialite->nom ?? 'Généraliste' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-400">Aucun médecin dans la base.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>