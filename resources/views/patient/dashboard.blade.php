@extends('layouts.master')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-blue-900 uppercase italic">Bienvenue, {{ $patient->prenom }}</h1>
    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Votre espace patient MonRDV</p>
</div>

{{-- STATISTIQUES --}}
<div class="grid grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
            <i class="fas fa-calendar text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-blue-900">{{ $totalRdv }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">Total RDV</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
            <i class="fas fa-clock text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-orange-500">{{ $rdvEnAttente }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">En attente</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500">
            <i class="fas fa-check-circle text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-green-600">{{ $rdvTermines }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">Termines</p>
        </div>
    </div>
</div>

{{-- BOUTON PRENDRE RDV --}}
<a href="{{ route('patient.prendre-rdv') }}"
   class="inline-flex items-center gap-3 bg-orange-500 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-lg hover:bg-orange-600 hover:shadow-xl hover:-translate-y-0.5 transition-all mb-8">
    <i class="fas fa-calendar-plus"></i> Prendre un nouveau rendez-vous
</a>

{{-- PROCHAINS RDV --}}
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
    <div class="p-6 border-b border-gray-50">
        <h2 class="text-sm font-black text-blue-900 uppercase">Prochains rendez-vous</h2>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($rdvsAVenir as $rdv)
        <div class="p-6 flex items-center justify-between hover:bg-gray-50/30 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex flex-col items-center justify-center">
                    <span class="text-xs font-black text-blue-900">{{ \Carbon\Carbon::parse($rdv->date_rv)->format('d') }}</span>
                    <span class="text-[8px] font-bold text-gray-400 uppercase">{{ \Carbon\Carbon::parse($rdv->date_rv)->translatedFormat('M') }}</span>
                </div>
                <div>
                    <p class="font-black text-blue-900 text-sm">Dr. {{ $rdv->medecin->nom }} {{ $rdv->medecin->prenom }}</p>
                    <p class="text-[10px] text-gray-400 font-bold">
                        {{ $rdv->medecin->specialite->nom ?? 'Generaliste' }}
                    </p>
                    @if($rdv->motif)
                        <p class="text-[10px] text-gray-500 mt-1">{{ $rdv->motif }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($rdv->statut === 'en_attente')
                    <span class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase"><i class="fas fa-clock mr-1"></i>En attente</span>
                @elseif($rdv->statut === 'confirme')
                    <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase"><i class="fas fa-check-circle mr-1"></i>Confirme</span>
                @endif
                <form action="{{ route('patient.annuler-rdv', $rdv->id) }}" method="POST" onsubmit="return confirm('Annuler ce rendez-vous ?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all" title="Annuler">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-calendar-times text-4xl text-gray-200 mb-3"></i>
            <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun rendez-vous a venir</p>
            <a href="{{ route('patient.prendre-rdv') }}" class="text-cyan-500 text-xs font-bold mt-2 inline-block hover:text-cyan-600">Prendre un rendez-vous</a>
        </div>
        @endforelse
    </div>
</div>

@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
