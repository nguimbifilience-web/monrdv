@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-blue-600 p-6 flex justify-between items-center">
            <h2 class="text-white text-xl font-bold italic">Modifier le Dossier : {{ $patient->nom }}</h2>
            <span class="text-blue-100 text-sm">ID Patient: #00{{ $patient->id }}</span>
        </div>
        
        <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="p-8">
            @csrf
            @method('PUT') {{-- Crucial pour la modification dans Laravel --}}

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nom</label>
                    <input type="text" name="nom" value="{{ old('nom', $patient->nom) }}" class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $patient->prenom) }}" class="w-full border-gray-200 rounded-xl p-3 bg-gray-50 focus:ring-2 focus:ring-blue-500 transition" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $patient->telephone) }}" class="w-full border-gray-200 rounded-xl p-3 bg-gray-50" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Quartier</label>
                    <input type="text" name="quartier" value="{{ old('quartier', $patient->quartier) }}" class="w-full border-gray-200 rounded-xl p-3 bg-gray-50">
                </div>
            </div>

            <div class="bg-blue-50 p-6 rounded-2xl mb-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-blue-800 mb-2">Assuré ?</label>
                        <select name="est_assure" id="select_assure" onchange="toggleAssurance(this.value)" class="w-full border-blue-200 rounded-xl p-3">
                            <option value="0" {{ $patient->est_assure == 0 ? 'selected' : '' }}>Non</option>
                            <option value="1" {{ $patient->est_assure == 1 ? 'selected' : '' }}>Oui</option>
                        </select>
                    </div>
                    <div id="bloc_assurance" style="display: {{ $patient->est_assure ? 'block' : 'none' }};">
                        <label class="block text-sm font-bold text-blue-800 mb-2">Assurance actuelle</label>
                        <select name="assurance_id" class="w-full border-blue-200 rounded-xl p-3">
                            @foreach($assurances as $a)
                                <option value="{{ $a->id }}" {{ $patient->assurance_id == $a->id ? 'selected' : '' }}>
                                    {{ $a->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-2">Médecin traitant</label>
                <select name="medecin_id" class="w-full border-gray-200 rounded-xl p-3 bg-gray-50">
                    <option value="">-- Changer de médecin --</option>
                    @foreach($medecins as $m)
                        <option value="{{ $m->id }}" {{ $patient->medecin_id == $m->id ? 'selected' : '' }}>
                            Dr. {{ $m->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white font-bold px-10 py-3 rounded-xl hover:bg-blue-700 shadow-lg">Mettre à jour</button>
                <a href="{{ route('patients.index') }}" class="py-3 px-6 text-gray-500 font-medium hover:text-red-500">Annuler les changements</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAssurance(val) {
    document.getElementById('bloc_assurance').style.display = (val === "1") ? "block" : "none";
}
</script>
@endsection