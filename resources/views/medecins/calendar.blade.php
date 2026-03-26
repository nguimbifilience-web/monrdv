@extends('layouts.master')

@section('content')
<div class="container py-4">
    <!-- Header avec infos du médecin -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold mb-0" style="color: #0d1e3a;">
                <i class="fas fa-calendar-check mr-2 text-primary"></i>
                Gestion du Planning : Dr. {{ $medecin->nom }}
            </h4>
            <span class="badge badge-pill badge-info px-3">{{ $medecin->specialite->nom ?? 'Généraliste' }}</span>
        </div>
        <a href="{{ route('medecins.schedule') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
        </a>
    </div>

    <!-- Carte du Calendrier -->
    <div class="card border-0 shadow-lg" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h6 class="text-muted small text-uppercase font-weight-bold">
                Astuce : Cliquez sur un jour pour l'inverser, ou glissez la souris pour sélectionner une période.
            </h6>
        </div>
        <div class="card-body p-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Styles et Scripts Externes -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
    /* Personnalisation du calendrier */
    .fc-daygrid-day { cursor: pointer; }
    .fc-daygrid-day:hover { background-color: #f8f9fa; }
    .fc-toolbar-title { font-weight: bold !important; color: #0d1e3a; text-transform: capitalize; }
    .fc-button-primary { background-color: #0d1e3a !important; border-color: #0d1e3a !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Configuration de la sécurité Axios (Token CSRF)
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    // 2. Initialisation de FullCalendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        selectable: true, // Permet de glisser pour sélectionner plusieurs jours
        selectMirror: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek'
        },

        // Charger les jours déjà enregistrés en vert
        events: [
            @foreach($medecin->disponibilites as $dispo)
            {
                id: 'event-{{ $dispo->date_travail }}',
                start: '{{ $dispo->date_travail }}',
                display: 'background',
                color: '#28a745' // Vert succès
            },
            @endforeach
        ],

        // GESTION DU CLIC SIMPLE (Cocher / Décocher)
        dateClick: function(info) {
            toggleDate(info.dateStr);
        },

        // GESTION DE LA SÉLECTION MULTIPLE (Glisser la souris)
        select: function(selectionInfo) {
            let start = new Date(selectionInfo.startStr);
            let end = new Date(selectionInfo.endStr);
            
            // On boucle sur chaque jour de la sélection
            let curr = new Date(start);
            while (curr < end) {
                let dateStr = curr.toISOString().split('T')[0];
                
                // On vérifie si le jour n'est pas déjà vert avant d'ajouter
                if (!calendar.getEventById('event-' + dateStr)) {
                    toggleDate(dateStr);
                }
                curr.setDate(curr.getDate() + 1);
            }
            calendar.unselect(); // Retire la surbrillance bleue
        }
    });

    // Fonction pour envoyer la donnée au serveur
    function toggleDate(dateStr) {
        axios.post('{{ route("dispo.toggle") }}', {
            date: dateStr,
            medecin_id: {{ $medecin->id }}
        })
        .then(response => {
            if (response.data.status === 'added') {
                // Ajouter le carré vert sans recharger
                calendar.addEvent({
                    id: 'event-' + dateStr,
                    start: dateStr,
                    display: 'background',
                    color: '#28a745'
                });
            } else if (response.data.status === 'removed') {
                // Retirer le carré vert sans recharger
                var event = calendar.getEventById('event-' + dateStr);
                if (event) event.remove();
            }
        })
        .catch(error => {
            console.error("Erreur:", error);
            alert("Impossible d'enregistrer la date. Vérifiez votre connexion.");
        });
    }

    calendar.render();
});
</script>
@endsection