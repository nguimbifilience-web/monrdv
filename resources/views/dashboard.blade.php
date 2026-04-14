@extends('layouts.master')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
    <p class="text-sm text-gray-500">Bienvenue, {{ auth()->user()->name }} — MonRDV</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

    {{-- Bouton Rendez-vous --}}
    <a href="{{ route('rendezvous.index') }}"
       class="group bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-2xl hover:border-orange-300 transition-all">
        <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-500 transition">
            <i class="fas fa-calendar-check text-2xl text-orange-500 group-hover:text-white transition"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Rendez-vous</h2>
        <p class="text-sm text-gray-500 mb-4">Planifier, consulter et gérer les rendez-vous médicaux</p>
        <div class="flex items-center justify-between">
            <span class="bg-orange-100 text-orange-600 text-xs font-bold px-3 py-1 rounded-full">
                {{ $nbRendezvous }} aujourd'hui
            </span>
            <i class="fas fa-arrow-right text-gray-300 group-hover:text-orange-500 transition"></i>
        </div>
    </a>

    {{-- Bouton Planning Médecin --}}
    <a href="{{ route('medecins.schedule') }}"
       class="group bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-2xl hover:border-blue-300 transition-all">
        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-900 transition">
            <i class="fas fa-user-md text-2xl text-blue-900 group-hover:text-white transition"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Planning Médecin</h2>
        <p class="text-sm text-gray-500 mb-4">Gérer les jours de travail et disponibilités des médecins</p>
        <div class="flex items-center justify-between">
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                {{ $nbMedecins }} médecins
            </span>
            <i class="fas fa-arrow-right text-gray-300 group-hover:text-blue-900 transition"></i>
        </div>
    </a>

    {{-- Bouton Patients --}}
    <a href="{{ route('patients.index') }}"
       class="group bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-2xl hover:border-green-300 transition-all">
        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-600 transition">
            <i class="fas fa-user-injured text-2xl text-green-600 group-hover:text-white transition"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Patients</h2>
        <p class="text-sm text-gray-500 mb-4">Créer, modifier et consulter les dossiers patients</p>
        <div class="flex items-center justify-between">
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                {{ $nbPatients }} patients
            </span>
            <i class="fas fa-arrow-right text-gray-300 group-hover:text-green-600 transition"></i>
        </div>
    </a>

</div>

{{-- SECTION STATISTIQUES --}}
@if(!empty($stats))
<div class="mt-10">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-black text-blue-900 uppercase">Statistiques</h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Mois en cours et 30 derniers jours</p>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase mb-2">Recettes du mois</p>
            <p class="text-2xl font-black text-gray-800">{{ number_format($stats['recettesMois'], 0, ',', ' ') }} F</p>
            @if($stats['evolution'] !== null)
                @php $cls = $stats['evolution'] >= 0 ? 'text-green-600' : 'text-red-500'; @endphp
                <p class="text-xs {{ $cls }} font-bold mt-1">
                    <i class="fas fa-arrow-{{ $stats['evolution'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($stats['evolution']) }}% vs mois précédent
                </p>
            @else
                <p class="text-xs text-gray-400 font-bold mt-1">Pas de référence</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase mb-2">Taux d'annulation (30j)</p>
            <p class="text-2xl font-black text-gray-800">{{ $stats['tauxAnnulation'] }}%</p>
            <p class="text-xs text-gray-400 font-bold mt-1">{{ $stats['totalRdv30j'] }} RDV au total</p>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase mb-2">Mois précédent (réf.)</p>
            <p class="text-2xl font-black text-gray-800">{{ number_format($stats['recettesMoisPrec'], 0, ',', ' ') }} F</p>
            <p class="text-xs text-gray-400 font-bold mt-1">Recettes patients</p>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
            <h3 class="text-sm font-black text-gray-600 uppercase mb-4">RDV par statut (30j)</h3>
            <canvas id="chart-statuts"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 lg:col-span-2">
            <h3 class="text-sm font-black text-gray-600 uppercase mb-4">Consultations sur 7 jours</h3>
            <canvas id="chart-consultations"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 lg:col-span-3">
            <h3 class="text-sm font-black text-gray-600 uppercase mb-4">Top médecins du mois</h3>
            @if(empty($stats['topMedecins']))
                <p class="text-sm text-gray-400 italic">Pas encore de consultations ce mois.</p>
            @else
                <canvas id="chart-medecins" height="80"></canvas>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const stats = @json($stats);
    const palette = ['#f97316', '#22c55e', '#ef4444', '#3b82f6', '#a855f7'];

    const statutLabels = { en_attente: 'En attente', confirme: 'Confirmé', termine: 'Terminé', annule: 'Annulé' };
    const statutsKeys = Object.keys(stats.rdvParStatut || {});
    if (statutsKeys.length) {
        new Chart(document.getElementById('chart-statuts'), {
            type: 'doughnut',
            data: {
                labels: statutsKeys.map(k => statutLabels[k] || k),
                datasets: [{
                    data: statutsKeys.map(k => stats.rdvParStatut[k]),
                    backgroundColor: palette,
                }],
            },
            options: { plugins: { legend: { position: 'bottom' } } },
        });
    }

    new Chart(document.getElementById('chart-consultations'), {
        type: 'bar',
        data: {
            labels: stats.consultations7j.map(d => d.label),
            datasets: [{
                label: 'Consultations',
                data: stats.consultations7j.map(d => d.total),
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }],
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
    });

    if ((stats.topMedecins || []).length) {
        new Chart(document.getElementById('chart-medecins'), {
            type: 'bar',
            data: {
                labels: stats.topMedecins.map(m => m.nom),
                datasets: [{
                    label: 'Consultations',
                    data: stats.topMedecins.map(m => m.total),
                    backgroundColor: '#22c55e',
                    borderRadius: 6,
                }],
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }
})();
</script>
@endif

{{-- SECTION GESTION DES COMPTES --}}
<div class="mt-10">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-black text-blue-900 uppercase">
                {{ auth()->user()->isAdmin() ? 'Comptes de la clinique' : 'Comptes patients' }}
            </h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $comptes->count() }} compte(s)</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('comptes.index') }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold uppercase">
            <i class="fas fa-cog mr-1"></i> Gestion complete
        </a>
        @endif
    </div>

    @if(session('reset_password'))
    @php $resetData = json_decode(session('reset_password')); @endphp
    <div class="mb-4 p-5 bg-yellow-50 border-2 border-yellow-300 rounded-2xl">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-key text-yellow-500"></i>
            <span class="font-black text-yellow-700 text-sm uppercase">Mot de passe reinitialise</span>
        </div>
        <div class="bg-white rounded-xl p-4 space-y-2">
            <p class="text-xs text-gray-500"><span class="font-bold">Nom :</span> {{ $resetData->name }}</p>
            <p class="text-xs text-gray-500"><span class="font-bold">Email :</span> {{ $resetData->email }}</p>
            <div class="flex items-center gap-3 mt-2">
                <span class="font-bold text-xs text-gray-500">Mot de passe :</span>
                <code class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-xl font-black text-lg tracking-widest" id="dashResetPwd">{{ $resetData->password }}</code>
                <button onclick="navigator.clipboard.writeText(document.getElementById('dashResetPwd').textContent); this.innerHTML='<i class=\'fas fa-check text-green-500\'></i> Copie !'; setTimeout(() => this.innerHTML='<i class=\'fas fa-copy\'></i> Copier', 2000)"
                    class="bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-yellow-300 transition-all">
                    <i class="fas fa-copy"></i> Copier
                </button>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-4">Utilisateur</th>
                    <th class="p-4">Email</th>
                    <th class="p-4 text-center">Role</th>
                    <th class="p-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($comptes as $compte)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-4">
                        <span class="font-bold text-blue-900 text-xs">{{ $compte->name }}</span>
                    </td>
                    <td class="p-4">
                        <span class="text-xs text-gray-600">{{ $compte->email }}</span>
                    </td>
                    <td class="p-4 text-center">
                        <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase
                            {{ $compte->role === 'admin' ? 'bg-orange-50 text-orange-600' : ($compte->role === 'medecin' ? 'bg-green-50 text-green-600' : ($compte->role === 'patient' ? 'bg-cyan-50 text-cyan-600' : 'bg-blue-50 text-blue-600')) }}">
                            {{ $compte->role }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        <button onclick="ouvrirResetDash({{ $compte->id }}, '{{ addslashes($compte->name) }}')"
                            class="w-8 h-8 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-all mx-auto" title="Reinitialiser mot de passe">
                            <i class="fas fa-key text-[10px]"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-10 text-center text-gray-400 text-xs italic">Aucun compte</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL RESET --}}
<div id="modalResetDash" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-black text-yellow-600 uppercase"><i class="fas fa-key mr-2"></i>Reinitialiser</h2>
            <button onclick="toggleModal('modalResetDash')" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-400 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-sm font-bold text-gray-600 mb-4">Compte : <span id="dashResetName" class="text-blue-900"></span></p>
        <form id="formResetDash" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nouveau mot de passe <span class="text-gray-300">(laisser vide = auto)</span></label>
                <input type="text" name="new_password"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-yellow-400 focus:ring-0 transition-all"
                    placeholder="Min. 8 caracteres ou laisser vide">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalResetDash')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="bg-yellow-500 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-yellow-600 transition-all">
                    <i class="fas fa-key mr-1"></i> Reinitialiser
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrirResetDash(id, name) {
    document.getElementById('dashResetName').textContent = name;
    document.getElementById('formResetDash').action = '/comptes/' + id + '/reset-password';
    toggleModal('modalResetDash');
}
</script>

{{-- Message flash --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
