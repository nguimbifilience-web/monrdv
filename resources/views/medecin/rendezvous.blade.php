@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-blue-900 uppercase italic">Mes Rendez-vous</h1>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Liste de vos consultations</p>
    </div>

    {{-- FILTRES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 mb-6">
        <form action="{{ route('medecin.rendezvous') }}" method="GET" class="flex items-center gap-4">
            <input type="date" name="date" value="{{ request('date') }}" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900">
            <select name="statut" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[160px]">
                <option value="">Tous les statuts</option>
                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Termine</option>
                <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annule</option>
            </select>
            <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px]">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
            @if(request()->anyFilled(['date', 'statut']))
                <a href="{{ route('medecin.rendezvous') }}" class="text-gray-400 hover:text-red-400 text-xs font-bold"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Date</th>
                    <th class="p-5">Heure</th>
                    <th class="p-5">Patient</th>
                    <th class="p-5">Telephone</th>
                    <th class="p-5">Motif</th>
                    <th class="p-5 text-center">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rendezvous as $rdv)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <span class="font-black text-blue-900 text-xs">{{ \Carbon\Carbon::parse($rdv->date_rv)->format('d/m/Y') }}</span>
                    </td>
                    <td class="p-5">
                        <span class="text-xs font-bold text-gray-600">{{ \Carbon\Carbon::parse($rdv->heure_rv)->format('H:i') }}</span>
                    </td>
                    <td class="p-5">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white text-[10px] font-black">
                                {{ strtoupper(substr($rdv->patient->prenom ?? '', 0, 1)) }}{{ strtoupper(substr($rdv->patient->nom ?? '', 0, 1)) }}
                            </div>
                            <span class="font-black text-blue-900 text-xs uppercase">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</span>
                        </div>
                    </td>
                    <td class="p-5 text-xs font-bold text-gray-600">{{ $rdv->patient->telephone }}</td>
                    <td class="p-5 text-xs text-gray-500 max-w-[200px]">{{ $rdv->motif ?? '—' }}</td>
                    <td class="p-5 text-center">
                        @if($rdv->statut === 'en_attente')
                            <span class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">En attente</span>
                        @elseif($rdv->statut === 'confirme')
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Confirme</span>
                        @elseif($rdv->statut === 'termine')
                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Termine</span>
                        @else
                            <span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Annule</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-20 text-center">
                        <i class="fas fa-calendar-times text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun rendez-vous</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($rendezvous->hasPages())
        <div class="p-4 bg-gray-50/50 border-t border-gray-50">
            {{ $rendezvous->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
