@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="mb-6">
        <a href="{{ route('rendezvous.index') }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour aux rendez-vous
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-50">
        <div class="bg-blue-900 p-8">
            <h2 class="text-white text-2xl font-black italic tracking-tighter">Nouveau Rendez-vous</h2>
            <p class="text-blue-300 text-xs font-bold uppercase tracking-widest mt-1">Remplissez les informations</p>
        </div>

        @if($errors->any())
        <div class="mx-8 mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
            @foreach($errors->all() as $error)
                <p class="text-red-600 text-xs font-bold">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('rendezvous.store') }}" method="POST" class="p-8">
            @csrf

            <div class="grid grid-cols-2 gap-6 mb-6">
                {{-- Patient --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Patient</label>
                    <div class="relative">
                        <input type="hidden" name="patient_id" id="patient_id_hidden" value="{{ old('patient_id') }}" required>
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                        <input type="text" id="patient_search" autocomplete="off" placeholder="Tapez un nom ou sélectionnez..."
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-10 pr-4 p-4 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0"
                            oninput="rechercherPatient()" onfocus="rechercherPatient()">
                        <div id="patient_dropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-2xl shadow-xl max-h-60 overflow-y-auto">
                        </div>
                    </div>
                </div>

                {{-- Médecin --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Médecin</label>
                    <select name="medecin_id" id="select_medecin" required onchange="chargerCreneaux()"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0">
                        <option value="">Sélectionner un médecin...</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ old('medecin_id') == $medecin->id ? 'selected' : '' }}>Dr. {{ $medecin->nom }} ({{ $medecin->specialite->nom ?? 'Généraliste' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                {{-- Date --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date du rendez-vous</label>
                    <input type="date" name="date_rv" id="input_date" value="{{ old('date_rv', date('Y-m-d')) }}" required
                        onchange="chargerCreneaux()"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0">
                </div>

                {{-- Heure (champ caché + sélection visuelle) --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Heure</label>
                    <input type="hidden" name="heure_rv" id="input_heure" value="{{ old('heure_rv') }}" required>
                    <div id="heureDisplay" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-gray-400 text-sm">
                        Sélectionnez un médecin et une date
                    </div>
                </div>
            </div>

            {{-- Créneaux disponibles --}}
            <div id="blocCreneaux" class="hidden mb-6">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Créneaux disponibles</label>

                <div id="alertNonTravail" class="hidden mb-3 p-3 bg-orange-50 border border-orange-200 rounded-xl text-orange-600 text-xs font-bold">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Ce médecin ne travaille pas ce jour-là
                </div>

                <div id="grilleCrenaux" class="grid grid-cols-5 gap-2"></div>
            </div>

            {{-- Motif --}}
            <div class="mb-8">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Motif (optionnel)</label>
                <textarea name="motif" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-gray-600 text-sm focus:border-cyan-400 focus:ring-0 resize-none" placeholder="Ex: Contrôle annuel, urgence...">{{ old('motif') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-900 text-white font-black px-10 py-4 rounded-2xl hover:bg-blue-800 shadow-lg transition-all uppercase tracking-widest text-xs">
                    <i class="fas fa-save mr-2"></i> Enregistrer le RDV
                </button>
                <a href="{{ route('rendezvous.index') }}" class="py-4 px-6 text-gray-400 font-bold hover:text-red-500 uppercase text-[10px]">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
let timerPatientSearch;
const allPatients = @json($patients->map(fn($p) => ['id' => $p->id, 'nom' => $p->nom, 'prenom' => $p->prenom, 'telephone' => $p->telephone]));

function rechercherPatient() {
    clearTimeout(timerPatientSearch);
    timerPatientSearch = setTimeout(() => {
        const search = document.getElementById('patient_search').value.toLowerCase().trim();
        const dropdown = document.getElementById('patient_dropdown');

        let resultats = allPatients;
        if (search.length > 0) {
            resultats = allPatients.filter(p =>
                (p.nom + ' ' + p.prenom).toLowerCase().includes(search) ||
                (p.prenom + ' ' + p.nom).toLowerCase().includes(search) ||
                (p.telephone || '').includes(search)
            );
        }

        if (resultats.length === 0) {
            dropdown.innerHTML = '<div class="p-4 text-center text-gray-400 text-xs font-bold">Aucun patient trouvé</div>';
        } else {
            dropdown.innerHTML = resultats.map(p => `
                <button type="button" onclick="selectPatient(${p.id}, '${p.nom.replace(/'/g,"\\'")} ${p.prenom.replace(/'/g,"\\'")}')"
                    class="w-full text-left px-4 py-3 hover:bg-blue-50 transition-colors flex justify-between items-center border-b border-gray-50 last:border-0">
                    <div>
                        <span class="font-black text-blue-900 text-xs uppercase">${p.nom} ${p.prenom}</span>
                    </div>
                    <span class="text-[10px] text-gray-400 font-bold">${p.telephone || ''}</span>
                </button>
            `).join('');
        }

        dropdown.classList.remove('hidden');
    }, 200);
}

function selectPatient(id, nom) {
    document.getElementById('patient_id_hidden').value = id;
    document.getElementById('patient_search').value = nom;
    document.getElementById('patient_dropdown').classList.add('hidden');
}

// Fermer le dropdown si on clique ailleurs
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('patient_dropdown');
    const input = document.getElementById('patient_search');
    if (!dropdown.contains(e.target) && e.target !== input) {
        dropdown.classList.add('hidden');
    }
});

function chargerCreneaux() {
    const medecinId = document.getElementById('select_medecin').value;
    const date = document.getElementById('input_date').value;

    if (!medecinId || !date) return;

    const bloc = document.getElementById('blocCreneaux');
    const grille = document.getElementById('grilleCrenaux');
    const alerte = document.getElementById('alertNonTravail');

    grille.innerHTML = '<p class="col-span-5 text-center text-gray-400 text-xs py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Chargement...</p>';
    bloc.classList.remove('hidden');
    alerte.classList.add('hidden');

    fetch(`/api/rendezvous/creneaux?medecin_id=${medecinId}&date=${date}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.travaille) {
            alerte.classList.remove('hidden');
        }

        grille.innerHTML = data.creneaux.map(c => {
            if (c.disponible) {
                return `<button type="button" onclick="selectCreneau(this, '${c.heure}')"
                    class="creneau py-3 rounded-xl text-xs font-black bg-green-50 text-green-600 border-2 border-green-200 hover:bg-green-500 hover:text-white hover:border-green-500 transition-all">
                    ${c.heure}
                </button>`;
            } else {
                return `<div class="py-3 rounded-xl text-xs font-bold bg-red-50 text-red-300 border border-red-100 text-center line-through">
                    ${c.heure}
                </div>`;
            }
        }).join('');
    });
}

function selectCreneau(btn, heure) {
    document.querySelectorAll('.creneau').forEach(b => {
        b.classList.remove('bg-blue-900', 'text-white', 'border-blue-900');
        b.classList.add('bg-green-50', 'text-green-600', 'border-green-200');
    });

    btn.classList.remove('bg-green-50', 'text-green-600', 'border-green-200');
    btn.classList.add('bg-blue-900', 'text-white', 'border-blue-900');

    document.getElementById('input_heure').value = heure;
    document.getElementById('heureDisplay').textContent = heure;
    document.getElementById('heureDisplay').classList.remove('text-gray-400');
    document.getElementById('heureDisplay').classList.add('text-blue-900');
}
</script>
@endsection
