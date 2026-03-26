@extends('layouts.master')

@section('content')
<div x-data="{ selectedMedecin: null }">

    {{-- Vue liste --}}
    <div x-show="!selectedMedecin">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Planning Médecin</h1>
                <p class="text-sm text-gray-500">Cliquez sur un médecin pour gérer ses jours de travail</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-blue-900 hover:text-orange-500 font-bold text-sm transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour au dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($medecins as $medecin)
            <button @click="selectedMedecin = {{ $medecin->id }}; $nextTick(() => window.dispatchEvent(new CustomEvent('render-cal', {detail: {{ $medecin->id }}})))"
                    class="bg-white rounded-xl shadow-md border border-gray-100 p-6 text-left hover:shadow-xl hover:border-orange-300 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-900 rounded-xl flex items-center justify-center text-white text-lg font-black group-hover:bg-orange-500 transition">
                        {{ strtoupper(substr($medecin->nom, 0, 1)) }}{{ strtoupper(substr($medecin->prenom, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-800 text-lg">Dr. {{ $medecin->nom }} {{ $medecin->prenom }}</h3>
                        <p class="text-orange-500 text-sm font-semibold">{{ $medecin->specialite->nom ?? 'Généraliste' }}</p>
                        <p class="text-gray-400 text-xs mt-1"><i class="fas fa-phone mr-1"></i>{{ $medecin->telephone ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold block">
                            {{ $medecin->disponibilites->count() }} jours
                        </span>
                        <i class="fas fa-chevron-right text-gray-300 mt-3 group-hover:text-orange-500 transition"></i>
                    </div>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Vue calendrier par médecin --}}
    @foreach($medecins as $medecin)
    <div x-show="selectedMedecin === {{ $medecin->id }}" x-transition>
        <div class="flex items-center gap-4 mb-6">
            <button @click="selectedMedecin = null" class="text-blue-900 hover:text-orange-500 font-bold text-sm transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="bg-blue-900 text-white p-5 flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-xl">Dr. {{ $medecin->nom }} {{ $medecin->prenom }}</h2>
                    <p class="text-orange-300 text-sm">{{ $medecin->specialite->nom ?? 'Généraliste' }}</p>
                </div>
                <span class="bg-green-500/20 text-green-300 text-sm font-bold px-4 py-2 rounded-full">
                    {{ $medecin->disponibilites->count() }} jours planifiés
                </span>
            </div>
            <div class="p-6">
                <p class="text-gray-500 text-sm mb-4">
                    <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                    Cliquez sur une date pour cocher/décocher un jour de travail
                </p>
                <div id="calendar-{{ $medecin->id }}"></div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var rendered = {};

    var calData = {
        @foreach($medecins as $medecin)
        {{ $medecin->id }}: [
            @foreach($medecin->disponibilites as $dispo)
            { start: '{{ $dispo->date_travail }}', display: 'background', color: '#22c55e' },
            @endforeach
        ],
        @endforeach
    };

    function renderCalendar(id) {
        if (rendered[id]) return;
        rendered[id] = true;

        var el = document.getElementById('calendar-' + id);
        if (!el) return;

        var cal = new FullCalendar.Calendar(el, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            selectable: true,
            height: 'auto',
            headerToolbar: { left: 'prev', center: 'title', right: 'next' },
            events: calData[id] || [],
            select: function(info) {
                axios.post('{{ route("dispo.toggle") }}', {
                    date: info.startStr,
                    medecin_id: id
                }).then(function() { location.reload(); });
            }
        });
        cal.render();
    }

    window.addEventListener('render-cal', function(e) {
        setTimeout(function() { renderCalendar(e.detail); }, 50);
    });
});
</script>

{{-- Message flash --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
