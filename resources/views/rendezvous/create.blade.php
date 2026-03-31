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
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <span class="bg-blue-900 text-white w-5 h-5 rounded-full inline-flex items-center justify-center text-[9px] mr-1">1</span>
                        Patient
                    </label>
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
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <span class="bg-blue-900 text-white w-5 h-5 rounded-full inline-flex items-center justify-center text-[9px] mr-1">2</span>
                        Médecin
                    </label>
                    <select name="medecin_id" id="select_medecin" required onchange="chargerPlanning()"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0">
                        <option value="">Sélectionner un médecin...</option>
                        @foreach($medecins as $medecin)
                            <option value="{{ $medecin->id }}" {{ old('medecin_id') == $medecin->id ? 'selected' : '' }}>Dr. {{ $medecin->nom }} ({{ $medecin->specialite->nom ?? 'Généraliste' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Planning FullCalendar du médecin --}}
            <div class="mb-6">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                    <span class="bg-blue-900 text-white w-5 h-5 rounded-full inline-flex items-center justify-center text-[9px] mr-1">3</span>
                    Choisir une date sur le planning
                </label>
                <div id="planning_container" class="bg-gray-50 rounded-2xl p-6 border-2 border-gray-100">
                    <p class="text-xs text-gray-400 italic text-center">
                        <i class="fas fa-calendar-alt mr-1"></i> Sélectionnez un médecin pour voir son planning.
                    </p>
                </div>
                <div id="date_selectionnee" class="hidden mt-3 p-3 bg-cyan-50 border-2 border-cyan-200 rounded-xl flex items-center gap-2">
                    <i class="fas fa-check-circle text-cyan-500"></i>
                    <span class="text-xs font-black text-cyan-700">Date sélectionnée : <span id="date_label"></span></span>
                </div>
                <input type="hidden" name="date_rv" id="input_date" value="{{ old('date_rv') }}">
                <input type="hidden" name="heure_rv" id="input_heure" value="">
            </div>

            {{-- Motif --}}
            <div class="mb-8">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                    <span class="bg-blue-900 text-white w-5 h-5 rounded-full inline-flex items-center justify-center text-[9px] mr-1">4</span>
                    Motif (optionnel)
                </label>
                <textarea name="motif" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-gray-600 text-sm focus:border-cyan-400 focus:ring-0 resize-none" placeholder="Ex: Contrôle annuel, urgence...">{{ old('motif') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" id="btnSubmit" disabled
                    class="bg-blue-900 text-white font-black px-10 py-4 rounded-2xl hover:bg-blue-800 shadow-lg transition-all uppercase tracking-widest text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i> Enregistrer le RDV
                </button>
                <a href="{{ route('rendezvous.index') }}" class="py-4 px-6 text-gray-400 font-bold hover:text-red-500 uppercase text-[10px]">Annuler</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>

<style>
    #fullcalendar .fc-daygrid-day.jour-dispo {
        background-color: #dcfce7 !important;
        cursor: pointer;
    }
    #fullcalendar .fc-daygrid-day.jour-dispo:hover {
        background-color: #bbf7d0 !important;
    }
    #fullcalendar .fc-daygrid-day.jour-complet {
        background-color: #fef2f2 !important;
        cursor: not-allowed;
    }
    #fullcalendar .fc-daygrid-day.jour-selectionne {
        background-color: #22d3ee !important;
    }
    #fullcalendar .fc-daygrid-day.jour-selectionne .fc-daygrid-day-number {
        color: white !important;
        font-weight: 900;
    }
</style>

<script>
let calendar = null;
let joursDispos = {};
let joursComplets = {};
let selectedDate = null;
let timerPatientSearch;
const allPatients = @json($patientsJson);
const aujourdhui = '{{ now()->format("Y-m-d") }}';

// ===== RECHERCHE PATIENT =====
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

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('patient_dropdown');
    const input = document.getElementById('patient_search');
    if (!dropdown.contains(e.target) && e.target !== input) {
        dropdown.classList.add('hidden');
    }
});

// ===== PLANNING FULLCALENDAR =====
function chargerPlanning() {
    const medecinId = document.getElementById('select_medecin').value;
    const container = document.getElementById('planning_container');

    selectedDate = null;
    document.getElementById('input_date').value = '';
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('date_selectionnee').classList.add('hidden');

    if (calendar) { calendar.destroy(); calendar = null; }

    if (!medecinId) {
        container.innerHTML = '<p class="text-xs text-gray-400 italic text-center"><i class="fas fa-calendar-alt mr-1"></i> Sélectionnez un médecin pour voir son planning.</p>';
        return;
    }

    container.innerHTML = '<p class="text-xs text-gray-400 italic text-center"><i class="fas fa-spinner fa-spin mr-1"></i> Chargement du planning...</p>';

    // On utilise la même API que le planning admin
    fetch(`/api/medecin/${medecinId}/planning`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        joursDispos = {};
        joursComplets = {};
        (data.dispos || []).forEach(d => joursDispos[d] = true);

        const rdvParDate = data.rdv_par_date || {};
        Object.entries(rdvParDate).forEach(([date, count]) => {
            if (count >= 15) joursComplets[date] = true;
        });

        container.innerHTML = `
            <div class="flex items-center gap-4 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-200 border-2 border-green-400 rounded"></div>
                    <span class="text-[9px] font-black text-gray-400 uppercase">Disponible</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-red-100 border-2 border-red-200 rounded"></div>
                    <span class="text-[9px] font-black text-gray-400 uppercase">Complet</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-cyan-400 rounded"></div>
                    <span class="text-[9px] font-black text-gray-400 uppercase">Sélectionné</span>
                </div>
                <span class="text-[9px] font-bold text-blue-900 ml-auto">${data.medecin}</span>
            </div>
            <div id="fullcalendar"></div>
        `;

        setTimeout(() => {
            const el = document.getElementById('fullcalendar');
            calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                height: 'auto',
                headerToolbar: { left: 'prev', center: 'title', right: 'next' },
                dateClick: function(info) {
                    const dateStr = info.dateStr;
                    if (joursDispos[dateStr] && !joursComplets[dateStr]) {
                        selectDate(dateStr);
                    }
                },
                dayCellDidMount: function(info) {
                    const dateStr = info.date.toISOString().split('T')[0];
                    if (joursDispos[dateStr]) {
                        if (joursComplets[dateStr]) {
                            info.el.classList.add('jour-complet');
                        } else {
                            info.el.classList.add('jour-dispo');
                        }
                    }
                    if (dateStr === selectedDate) {
                        info.el.classList.add('jour-selectionne');
                    }
                }
            });
            calendar.render();
        }, 50);
    })
    .catch(() => {
        container.innerHTML = '<p class="text-xs text-red-400 font-bold text-center">Erreur de chargement du planning.</p>';
    });
}

function selectDate(dateStr) {
    selectedDate = dateStr;
    document.getElementById('input_date').value = dateStr;
    document.getElementById('btnSubmit').disabled = false;

    const d = new Date(dateStr + 'T00:00:00');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('date_label').textContent = d.toLocaleDateString('fr-FR', options);
    document.getElementById('date_selectionnee').classList.remove('hidden');

    document.querySelectorAll('#fullcalendar .fc-daygrid-day').forEach(cell => {
        cell.classList.remove('jour-selectionne');
        if (cell.dataset.date === dateStr) {
            cell.classList.add('jour-selectionne');
        }
    });
}
</script>
@endpush
@endsection
