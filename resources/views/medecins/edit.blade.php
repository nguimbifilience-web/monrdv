@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-100/50 overflow-hidden border border-gray-50">
        <div class="bg-blue-600 p-8 flex justify-between items-center">
            <div>
                <h2 class="text-white text-2xl font-black italic tracking-tighter">Mise à jour du Praticien</h2>
                <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mt-1">Modification du profil de Dr. {{ $medecin->nom }}</p>
            </div>
            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-user-md"></i>
            </div>
        </div>

        <form action="{{ route('medecins.update', $medecin->id) }}" method="POST" class="p-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Nom du Médecin</label>
                    <input type="text" name="nom" value="{{ old('nom', $medecin->nom) }}"
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-0 transition-all font-bold text-gray-700" required>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $medecin->prenom) }}"
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-0 transition-all font-bold text-gray-700" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Spécialité médicale</label>
                    <select name="specialite_id"
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-0 transition-all font-bold text-gray-700" required>
                        <option value="">-- Choisir une spécialité --</option>
                        @foreach($specialites as $spec)
                            <option value="{{ $spec->id }}" {{ old('specialite_id', $medecin->specialite_id) == $spec->id ? 'selected' : '' }}>
                                {{ $spec->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Numéro de téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $medecin->telephone) }}"
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-0 transition-all font-bold text-gray-700">
                </div>
            </div>

            {{-- Tarification --}}
            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 mb-8">
                <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
                    <i class="fas fa-money-bill-wave text-cyan-500 mr-2"></i> Tarification mensuelle
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Tarif par heure (FCFA)</label>
                        <input type="number" name="tarif_heure" value="{{ old('tarif_heure', $medecin->tarif_heure) }}" min="0" step="500" required
                            id="tarif_heure" oninput="calculerTotal()"
                            class="w-full border-2 border-blue-200 rounded-2xl p-4 bg-white focus:border-blue-400 focus:ring-0 font-black text-blue-900">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Heures par mois</label>
                        <input type="number" name="heures_mois" value="{{ old('heures_mois', $medecin->heures_mois) }}" min="0" required
                            id="heures_mois" oninput="calculerTotal()"
                            class="w-full border-2 border-blue-200 rounded-2xl p-4 bg-white focus:border-blue-400 focus:ring-0 font-black text-blue-900">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-green-600 uppercase mb-2">Montant total / mois</label>
                        <div id="montant_total_display" class="w-full p-4 rounded-2xl bg-green-50 border-2 border-green-200 font-black text-green-700 text-lg">
                            {{ number_format(($medecin->tarif_heure ?? 0) * ($medecin->heures_mois ?? 0), 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6 border-t border-gray-50">
                <button type="submit" class="bg-blue-600 text-white font-black px-10 py-4 rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all flex items-center uppercase tracking-widest text-xs">
                    <i class="fas fa-save mr-3"></i> Enregistrer les modifications
                </button>
                <a href="{{ route('medecins.index') }}" class="text-gray-400 font-bold hover:text-red-500 transition-colors uppercase tracking-widest text-[10px]">
                    Annuler et revenir
                </a>
            </div>
        </form>
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
