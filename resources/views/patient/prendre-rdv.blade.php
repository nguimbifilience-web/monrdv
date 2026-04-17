@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('patient.dashboard') }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-50">
        <div class="bg-gradient-to-r from-blue-900 to-blue-800 py-6 px-8">
            <h4 class="text-white text-xl font-bold tracking-widest uppercase">
                <i class="fas fa-calendar-plus mr-2"></i> Prendre Rendez-vous
            </h4>
        </div>

        <div class="p-10">
            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
                @foreach($errors->all() as $error)
                    <p class="text-xs font-bold">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form action="{{ route('patient.store-rdv') }}" method="POST" id="rdvForm">
                @csrf

                {{-- Etape 1 : Choix du medecin --}}
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <span class="bg-blue-900 text-white w-5 h-5 rounded-full inline-flex items-center justify-center text-[9px] mr-1">1</span>
                        Choisir un medecin
                    </label>
                    <select name="medecin_id" id="medecin_id" required onchange="chargerPlanning()"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all">
                        <option value="">-- Selectionnez un medecin --</option>
                        @foreach($medecins as $m)
                            <option value="{{ $m->id }}" {{ old('medecin_id') == $m->id ? 'selected' : '' }}>
                                Dr. {{ $m->nom }} {{ $m->prenom }} — {{ $m->specialite->nom ?? 'Generaliste' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Etape 2 : Planning calendrier du medecin --}}
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <span class="bg-blue-900 text-white w-5 h-5 rounded-full inline-flex items-center justify-center text-[9px] mr-1">2</span>
                        Choisir une date sur le planning du medecin
                    </label>
                    <div id="planning_container" class="bg-gray-50 rounded-2xl p-6 border-2 border-gray-100">
                        <p class="text-xs text-gray-400 italic text-center">
                            <i class="fas fa-calendar-alt mr-1"></i> Selectionnez un medecin pour voir son planning.
                        </p>
                    </div>
                    <div id="date_selectionnee" class="hidden mt-3 p-3 bg-cyan-50 border-2 border-cyan-200 rounded-xl flex items-center gap-2">
                        <i class="fas fa-check-circle text-cyan-500"></i>
                        <span class="text-xs font-black text-cyan-700">Date selectionnee : <span id="date_label"></span></span>
                    </div>
                    <input type="hidden" name="date_rv" id="date_rv" value="{{ old('date_rv') }}">
                </div>

                {{-- Etape 3 : Motif (obligatoire) --}}
                <div class="mb-8">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <span class="bg-blue-900 text-white w-5 h-5 rounded-full inline-flex items-center justify-center text-[9px] mr-1">3</span>
                        Motif de la consultation <span class="text-red-400">*</span>
                    </label>
                    <select id="motif_select" onchange="onMotifSelect()"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0 mb-2">
                        <option value="">— Selectionnez un motif ou saisissez ci-dessous —</option>
                    </select>
                    <textarea name="motif" id="motif_text" rows="3" required
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 text-sm focus:border-blue-500 focus:bg-white focus:ring-0 transition-all"
                        placeholder="Ou saisissez un motif libre...">{{ old('motif') }}</textarea>
                </div>

                <div class="flex gap-4">
                    <button type="submit" id="btnSubmit" disabled
                        class="bg-blue-900 hover:bg-blue-800 text-white font-black px-10 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane mr-2"></i> Envoyer la demande
                    </button>
                    <a href="{{ route('patient.dashboard') }}" class="py-4 px-6 text-gray-400 font-bold hover:text-red-500 uppercase text-[10px]">Annuler</a>
                </div>
            </form>
        </div>
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
const aujourdhui = '{{ now()->format("Y-m-d") }}';

function chargerPlanning() {
    const medecinId = document.getElementById('medecin_id').value;
    const container = document.getElementById('planning_container');

    selectedDate = null;
    document.getElementById('date_rv').value = '';
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('date_selectionnee').classList.add('hidden');

    if (calendar) { calendar.destroy(); calendar = null; }
    chargerMotifs();

    if (!medecinId) {
        container.innerHTML = '<p class="text-xs text-gray-400 italic text-center"><i class="fas fa-calendar-alt mr-1"></i> Selectionnez un medecin pour voir son planning.</p>';
        return;
    }

    container.innerHTML = '<p class="text-xs text-gray-400 italic text-center"><i class="fas fa-spinner fa-spin mr-1"></i> Chargement du planning...</p>';

    fetch(`/espace-patient/ajax/medecin/${medecinId}/disponibilites`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        joursDispos = {};
        joursComplets = {};
        (data.dispos || []).forEach(d => joursDispos[d] = true);

        const rdvParDate = data.rdv_par_date || {};
        Object.entries(rdvParDate).forEach(([date, count]) => {
            if (count >= 20) joursComplets[date] = true;
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
                    <span class="text-[9px] font-black text-gray-400 uppercase">Votre choix</span>
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
                validRange: { start: aujourdhui },
                dateClick: function(info) {
                    const dateStr = info.dateStr;
                    if (joursDispos[dateStr] && !joursComplets[dateStr] && dateStr >= aujourdhui) {
                        selectDate(dateStr);
                    }
                },
                dayCellDidMount: function(info) {
                    const dateStr = info.date.toISOString().split('T')[0];
                    if (joursDispos[dateStr] && dateStr >= aujourdhui) {
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

// ===== MOTIFS PAR SPÉCIALITÉ =====
function chargerMotifs() {
    const medecinId = document.getElementById('medecin_id').value;
    const select = document.getElementById('motif_select');
    select.innerHTML = '<option value="">— Selectionnez un motif ou saisissez ci-dessous —</option>';
    if (!medecinId) return;

    fetch(`/ajax/rendezvous/motifs?medecin_id=${medecinId}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            (data.motifs || []).forEach(m => {
                const opt = document.createElement('option');
                opt.value = m;
                opt.textContent = m;
                select.appendChild(opt);
            });
            const optAutre = document.createElement('option');
            optAutre.value = '__autre__';
            optAutre.textContent = 'Autre (saisie libre)';
            select.appendChild(optAutre);
        });
}

function onMotifSelect() {
    const select = document.getElementById('motif_select');
    const textarea = document.getElementById('motif_text');
    if (select.value && select.value !== '__autre__') {
        textarea.value = select.value;
    } else if (select.value === '__autre__') {
        textarea.value = '';
        textarea.focus();
    }
}

function selectDate(dateStr) {
    selectedDate = dateStr;
    document.getElementById('date_rv').value = dateStr;
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
