@extends('layouts.master')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('medecins.index') }}" class="text-gray-500 hover:text-blue-600 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
        <div class="bg-blue-600 py-6 text-center">
            <h4 class="text-white text-xl font-bold tracking-widest uppercase">Nouveau Praticien</h4>
        </div>

        <div class="p-10">
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
                    <p class="font-bold">Oups ! Il y a des erreurs :</p>
                    <ul class="mt-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medecins.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Nom du Médecin</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Ex: NDONG"
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Ex: Paul"
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Spécialité</label>
                        <select name="specialite_id" required
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm">
                            <option value="">Sélectionner une spécialité</option>
                            @foreach($specialites as $specialite)
                                <option value="{{ $specialite->id }}" {{ old('specialite_id') == $specialite->id ? 'selected' : '' }}>
                                    {{ $specialite->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="Ex: 074 00 00 00"
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm">
                    </div>
                </div>

                {{-- Tarification --}}
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 mb-6">
                    <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
                        <i class="fas fa-money-bill-wave text-cyan-500 mr-2"></i> Tarification mensuelle
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Tarif par heure (FCFA)</label>
                            <input type="number" name="tarif_heure" value="{{ old('tarif_heure', 0) }}" min="0" step="500" required
                                id="tarif_heure" oninput="calculerTotal()"
                                class="w-full px-4 py-3 rounded-xl bg-white border border-blue-200 focus:border-blue-500 focus:ring-0 font-black text-blue-900">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Heures par mois</label>
                            <input type="number" name="heures_mois" value="{{ old('heures_mois', 0) }}" min="0" required
                                id="heures_mois" oninput="calculerTotal()"
                                class="w-full px-4 py-3 rounded-xl bg-white border border-blue-200 focus:border-blue-500 focus:ring-0 font-black text-blue-900">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-green-600 uppercase mb-2">Montant total / mois</label>
                            <div id="montant_total_display" class="w-full px-4 py-3 rounded-xl bg-green-50 border border-green-200 font-black text-green-700 text-lg">
                                0 FCFA
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-full shadow-lg transform hover:-translate-y-1 transition duration-200">
                        <i class="fas fa-check-circle mr-2"></i> ENREGISTRER LE PROFIL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculerTotal() {
    const tarif = parseFloat(document.getElementById('tarif_heure').value) || 0;
    const heures = parseFloat(document.getElementById('heures_mois').value) || 0;
    const total = tarif * heures;
    document.getElementById('montant_total_display').textContent = total.toLocaleString('fr-FR') + ' FCFA';
}
</script>
@endsection
