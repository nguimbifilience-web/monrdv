@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Mon Planning</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Dr. {{ $medecin->nom }} {{ $medecin->prenom }} — {{ $medecin->specialite->nom ?? 'Generaliste' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-green-50 text-green-600 px-4 py-2 rounded-xl text-xs font-black">
                <i class="fas fa-calendar-check mr-1"></i> {{ count($dispos) }} jours de travail
            </span>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden p-8">
        {{-- Legende --}}
        <div class="flex items-center gap-6 mb-6">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-200 border-2 border-green-400 rounded"></div>
                <span class="text-[9px] font-black text-gray-400 uppercase">Jour de travail</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-200 border-2 border-blue-400 rounded"></div>
                <span class="text-[9px] font-black text-gray-400 uppercase">Jour avec RDV</span>
            </div>
        </div>

        {{-- FullCalendar --}}
        <div id="fullcalendar"></div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>

<style>
    #fullcalendar .fc-daygrid-day.jour-travail {
        background-color: #dcfce7 !important;
    }
    #fullcalendar .fc-daygrid-day.jour-travail:hover {
        background-color: #bbf7d0 !important;
    }
    #fullcalendar .fc-daygrid-day.jour-rdv {
        background-color: #dbeafe !important;
    }
    #fullcalendar .rdv-badge {
        background-color: #3b82f6;
        color: white;
        border-radius: 6px;
        padding: 1px 6px;
        font-size: 10px;
        font-weight: 900;
        margin-top: 2px;
        display: inline-block;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dispos = @json($dispos);
    const rdvParDate = @json($rdvParDate);

    const joursDispos = {};
    dispos.forEach(d => joursDispos[d] = true);

    // Creer les events pour les jours avec RDV
    const events = [];
    Object.entries(rdvParDate).forEach(([date, count]) => {
        events.push({
            title: count + ' RDV',
            start: date,
            allDay: true,
            display: 'block',
            backgroundColor: '#3b82f6',
            borderColor: '#3b82f6',
            textColor: '#fff',
            classNames: ['rdv-badge']
        });
    });

    const el = document.getElementById('fullcalendar');
    const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
        headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },
        events: events,
        dayCellDidMount: function(info) {
            const dateStr = info.date.toISOString().split('T')[0];
            if (joursDispos[dateStr]) {
                info.el.classList.add('jour-travail');
                if (rdvParDate[dateStr]) {
                    info.el.classList.add('jour-rdv');
                }
            }
        }
    });
    calendar.render();
});
</script>
@endsection
