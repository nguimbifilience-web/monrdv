@extends('layouts.master')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('patients.index') }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-50">
        <div class="bg-blue-600 py-6 px-8">
            <h4 class="text-white text-xl font-bold tracking-widest uppercase">Nouveau Patient</h4>
        </div>

        <div class="p-10">
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
                    @foreach ($errors->all() as $error)
                        <p class="text-xs font-bold">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="patientForm" action="{{ route('patients.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom</label>
                        <input type="text" name="nom" id="input_nom" value="{{ old('nom') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Ex: NDONG">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Prénom</label>
                        <input type="text" name="prenom" id="input_prenom" value="{{ old('prenom') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Ex: Paul">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Téléphone</label>
                        <input type="text" name="telephone" id="input_telephone" value="{{ old('telephone') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Ex: 074 00 00 00">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email (Optionnel — pour créer un compte patient)</label>
                        <div class="relative">
                            <input type="email" name="email" id="input_email" value="{{ old('email') }}"
                                oninput="verifierEmail()"
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="patient@email.com">
                            <span id="emailStatus" class="absolute right-4 top-1/2 -translate-y-1/2 text-sm hidden"></span>
                        </div>
                        <p id="emailMsg" class="text-[10px] font-bold mt-1 hidden"></p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quartier</label>
                    <input type="text" name="quartier" value="{{ old('quartier') }}"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Quartier...">
                </div>

                {{-- Assurance --}}
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 mb-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Le patient est-il assuré ?</label>
                            <select name="est_assure" id="est_assure_select" onchange="toggleAssurance(this.value)"
                                class="w-full bg-white border-2 border-blue-200 rounded-2xl p-4 font-bold text-blue-900 focus:ring-0">
                                <option value="0" {{ old('est_assure') == '0' ? 'selected' : '' }}>Non</option>
                                <option value="1" {{ old('est_assure') == '1' ? 'selected' : '' }}>Oui</option>
                            </select>
                        </div>
                        <div id="bloc_assurance" style="display: none;">
                            <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Assurance</label>
                            <select name="assurance_id" class="w-full bg-white border-2 border-blue-200 rounded-2xl p-4 font-bold text-blue-900 focus:ring-0">
                                <option value="">-- Sélectionner --</option>
                                @foreach($assurances as $a)
                                    <option value="{{ $a->id }}" {{ old('assurance_id') == $a->id ? 'selected' : '' }}>{{ $a->nom }} ({{ $a->taux_couverture }}%)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Médecin traitant</label>
                    <select name="medecin_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                        <option value="">-- Aucun médecin assigné --</option>
                        @foreach($medecins as $m)
                            <option value="{{ $m->id }}" {{ old('medecin_id') == $m->id ? 'selected' : '' }}>Dr. {{ $m->nom }} {{ $m->prenom }} — {{ $m->specialite->nom ?? 'Généraliste' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-10 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                        <i class="fas fa-check-circle mr-2"></i> Enregistrer le patient
                    </button>
                    <a href="{{ route('patients.index') }}" class="py-4 px-6 text-gray-400 font-bold hover:text-red-500 uppercase text-[10px]">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAssurance(val) {
    document.getElementById('bloc_assurance').style.display = (val === "1") ? "block" : "none";
}
window.onload = function() {
    toggleAssurance(document.getElementById('est_assure_select').value);
};

let emailTimer;
function verifierEmail() {
    clearTimeout(emailTimer);
    const email = document.getElementById('input_email').value;
    const status = document.getElementById('emailStatus');
    const msg = document.getElementById('emailMsg');

    if (!email || !email.includes('@')) {
        status.classList.add('hidden');
        msg.classList.add('hidden');
        return;
    }

    emailTimer = setTimeout(() => {
        fetch(`/ajax/patients/check-email?email=${encodeURIComponent(email)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            status.classList.remove('hidden');
            msg.classList.remove('hidden');

            if (data.exists) {
                status.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                msg.textContent = 'Cet email est déjà utilisé';
                msg.className = 'text-[10px] font-bold mt-1 text-red-500';
            } else {
                status.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                msg.textContent = 'Email disponible';
                msg.className = 'text-[10px] font-bold mt-1 text-green-500';
            }
        })
        .catch(() => {});
    }, 500);
}
</script>
@endsection
