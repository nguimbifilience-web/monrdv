@extends('layouts.master')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Rendez-vous</h1>
        <p class="text-sm text-gray-500">{{ $rendezvous->count() }} rendez-vous ce mois</p>
    </div>
    <a href="{{ route('rendezvous.create') }}"
       class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition flex items-center gap-2">
        <i class="fas fa-plus"></i> Nouveau Rendez-vous
    </a>
</div>

{{-- Filtres --}}
<form action="{{ route('rendezvous.index') }}" method="GET"
      class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[180px]">
        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Patient</label>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du patient..."
               class="w-full border-gray-200 rounded-lg bg-gray-50 text-sm focus:ring-orange-500 focus:border-orange-500">
    </div>
    <div class="min-w-[160px]">
        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Date</label>
        <input type="date" name="date" value="{{ request('date') }}"
               class="w-full border-gray-200 rounded-lg bg-gray-50 text-sm focus:ring-orange-500 focus:border-orange-500">
    </div>
    <div class="min-w-[180px]">
        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Médecin</label>
        <select name="medecin_id" class="w-full border-gray-200 rounded-lg bg-gray-50 text-sm focus:ring-orange-500">
            <option value="">Tous</option>
            @foreach($medecins as $m)
                <option value="{{ $m->id }}" {{ request('medecin_id') == $m->id ? 'selected' : '' }}>Dr. {{ $m->nom }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-blue-900 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-blue-800 transition">
        <i class="fas fa-search mr-1"></i> Filtrer
    </button>
    @if(request()->anyFilled(['search','date','medecin_id']))
        <a href="{{ route('rendezvous.index') }}" class="text-red-500 text-xs font-bold hover:underline self-center">
            <i class="fas fa-times"></i> Réinitialiser
        </a>
    @endif
</form>

{{-- Tableau --}}
<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-100 text-gray-400">
            <tr>
                <th class="p-4 text-xs font-bold uppercase tracking-wider">Date</th>
                <th class="p-4 text-xs font-bold uppercase tracking-wider">Heure</th>
                <th class="p-4 text-xs font-bold uppercase tracking-wider">Patient</th>
                <th class="p-4 text-xs font-bold uppercase tracking-wider">Médecin</th>
                <th class="p-4 text-xs font-bold uppercase tracking-wider">Motif</th>
                <th class="p-4 text-xs font-bold uppercase tracking-wider text-center">Statut</th>
                <th class="p-4 text-xs font-bold uppercase tracking-wider text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
            @forelse($rendezvous as $rdv)
                <tr class="hover:bg-blue-50/50 transition">
                    <td class="p-4 font-bold text-gray-800">{{ \Carbon\Carbon::parse($rdv->date_rv)->format('d/m/Y') }}</td>
                    <td class="p-4 text-blue-900 font-bold">
                        <i class="fas fa-clock text-orange-400 mr-1"></i>{{ $rdv->heure_rv }}
                    </td>
                    <td class="p-4 font-bold text-gray-800">{{ strtoupper($rdv->patient->nom ?? '') }} {{ $rdv->patient->prenom ?? '' }}</td>
                    <td class="p-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">Dr. {{ $rdv->medecin->nom ?? '' }}</span>
                    </td>
                    <td class="p-4 text-gray-600">{{ $rdv->motif ?? '-' }}</td>
                    <td class="p-4 text-center">
                        @if($rdv->statut === 'confirme')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Confirmé</span>
                        @elseif($rdv->statut === 'annule')
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Annulé</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold">En attente</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('rendezvous.edit', $rdv) }}" class="text-orange-400 hover:text-orange-600 p-2 hover:bg-orange-100 rounded-full transition" title="Modifier">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('rendezvous.destroy', $rdv) }}" method="POST" onsubmit="return confirm('Supprimer ce rendez-vous ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-100 rounded-full transition" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-10 text-center text-gray-400">
                        <i class="fas fa-calendar-times text-4xl mb-3 block"></i>
                        <p>Aucun rendez-vous trouvé.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Message flash --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
