@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Planning Médecins</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Gestion des jours de travail</p>
        </div>
    </div>

    {{-- LISTE DES MÉDECINS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @foreach($medecins as $medecin)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5 flex items-center justify-between hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black shadow-md shadow-blue-100">
                    <i class="fas fa-user-md"></i>
                </div>
                <div>
                    <h3 class="font-black text-blue-900 text-sm">Dr. {{ strtoupper($medecin->nom) }} {{ $medecin->prenom }}</h3>
                    <span class="bg-indigo-50 text-indigo-500 px-2 py-0.5 rounded text-[9px] font-black uppercase">{{ $medecin->specialite->nom ?? 'Généraliste' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-[10px] font-black" id="badge-{{ $medecin->id }}">
                    {{ $medecin->disponibilites->count() }} jours
                </span>
                <button onclick="ouvrirCalendrier({{ $medecin->id }}, '{{ addslashes($medecin->nom) }} {{ addslashes($medecin->prenom) }}', '{{ addslashes($medecin->specialite->nom ?? 'Généraliste') }}')"
                    class="w-10 h-10 flex items-center justify-center bg-cyan-50 text-cyan-500 rounded-xl hover:bg-cyan-500 hover:text-white transition-all" title="Calendrier">
                    <i class="fas fa-calendar-alt"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- MODAL CALENDRIER --}}
<div id="modalCalendrier" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-xl p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 id="cal_medecin_nom" class="text-lg font-black text-blue-900 uppercase"></h2>
                <span id="cal_medecin_spec" class="text-[10px] font-black text-indigo-500 uppercase"></span>
            </div>
            <button onclick="fermerCalendrier()" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-400 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex items-center gap-4 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-200 border-2 border-green-500 rounded"></div>
                <span class="text-[9px] font-black text-gray-400 uppercase">Jour de travail</span>
            </div>
        </div>
        <div id="fullcalendar"></div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    #fullcalendar .fc-daygrid-day.jour-travail {
        background-color: #dcfce7 !important;
    }
    #fullcalendar .fc-daygrid-day {
        cursor: pointer;
    }
    #fullcalendar .fc-daygrid-day:hover {
        background-color: #f0f9ff !important;
    }
    #fullcalendar .fc-daygrid-day.jour-travail:hover {
        background-color: #bbf7d0 !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>
<script>
    let calendar = null;
    let currentMedecinId = null;
    let joursActifs = {};

    const dispoData = {
        @foreach($medecins as $medecin)
        {{ $medecin->id }}: {
            dates: [ @foreach($medecin->disponibilites as $dispo)'{{ $dispo->date_travail }}',@endforeach ],
            count: {{ $medecin->disponibilites->count() }}
        },
        @endforeach
    };

    function ouvrirCalendrier(medecinId, nom, specialite) {
        if (typeof FullCalendar === 'undefined') {
            setTimeout(() => ouvrirCalendrier(medecinId, nom, specialite), 50);
            return;
        }

        currentMedecinId = medecinId;
        document.getElementById('cal_medecin_nom').textContent = 'Dr. ' + nom;
        document.getElementById('cal_medecin_spec').textContent = specialite;

        joursActifs = {};
        (dispoData[medecinId]?.dates || []).forEach(d => joursActifs[d] = true);

        document.getElementById('modalCalendrier').classList.remove('hidden');

        if (calendar) { calendar.destroy(); calendar = null; }

        setTimeout(function() {
            const el = document.getElementById('fullcalendar');
            calendar = new FullCalendar.Calendar(el, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                height: 'auto',
                headerToolbar: { left: 'prev', center: 'title', right: 'next' },
                dateClick: function(info) {
                    toggleJour(info.dateStr);
                },
                dayCellDidMount: function(info) {
                    const dateStr = info.date.toISOString().split('T')[0];
                    if (joursActifs[dateStr]) {
                        info.el.classList.add('jour-travail');
                    }
                }
            });
            calendar.render();
        }, 100);
    }

    function fermerCalendrier() {
        document.getElementById('modalCalendrier').classList.add('hidden');
        if (calendar) { calendar.destroy(); calendar = null; }
    }

    function toggleJour(dateStr) {
        fetch('{{ route("dispo.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ medecin_id: currentMedecinId, date: dateStr })
        })
        .then(r => r.json())
        .then(data => {
            const cells = document.querySelectorAll('#fullcalendar .fc-daygrid-day');
            cells.forEach(cell => {
                if (cell.dataset.date === dateStr) {
                    if (data.status === 'added') {
                        cell.classList.add('jour-travail');
                        joursActifs[dateStr] = true;
                    } else {
                        cell.classList.remove('jour-travail');
                        delete joursActifs[dateStr];
                    }
                }
            });

            const count = Object.keys(joursActifs).length;
            const badge = document.getElementById('badge-' + currentMedecinId);
            if (badge) badge.textContent = count + ' jours';
            dispoData[currentMedecinId].dates = Object.keys(joursActifs);
            dispoData[currentMedecinId].count = count;
        });
    }
</script>
@endpush
@endsection
