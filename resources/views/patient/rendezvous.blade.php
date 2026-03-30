@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Mes Rendez-vous</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Historique de vos rendez-vous</p>
        </div>
        <a href="{{ route('patient.prendre-rdv') }}" class="bg-cyan-400 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-lg hover:scale-105 transition-all">
            + Nouveau RDV
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Date</th>
                    <th class="p-5">Heure</th>
                    <th class="p-5">Medecin</th>
                    <th class="p-5">Specialite</th>
                    <th class="p-5">Motif</th>
                    <th class="p-5 text-center">Statut</th>
                    <th class="p-5 text-center">Action</th>
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
                        <span class="font-black text-blue-900 text-xs">Dr. {{ $rdv->medecin->nom ?? '' }} {{ $rdv->medecin->prenom ?? '' }}</span>
                    </td>
                    <td class="p-5">
                        <span class="bg-indigo-50 text-indigo-500 px-2 py-1 rounded text-[9px] font-black uppercase">
                            {{ $rdv->medecin->specialite->nom ?? 'Generaliste' }}
                        </span>
                    </td>
                    <td class="p-5 text-xs text-gray-500 max-w-[200px]">{{ $rdv->motif ?? '—' }}</td>
                    <td class="p-5 text-center">
                        @if($rdv->statut === 'en_attente')
                            <span class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                <i class="fas fa-clock mr-1"></i>En attente
                            </span>
                        @elseif($rdv->statut === 'confirme')
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                <i class="fas fa-check-circle mr-1"></i>Confirme
                            </span>
                        @elseif($rdv->statut === 'termine')
                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Termine</span>
                        @else
                            <span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Annule</span>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        @if($rdv->statut === 'en_attente')
                        <form action="{{ route('patient.annuler-rdv', $rdv->id) }}" method="POST" onsubmit="return confirm('Annuler ce rendez-vous ?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all mx-auto" title="Annuler">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-20 text-center">
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

@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
