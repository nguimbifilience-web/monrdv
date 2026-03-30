@extends('layouts.master')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-blue-900 uppercase italic">Bonjour, Dr. {{ $medecin->nom }}</h1>
    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $medecin->specialite->nom ?? 'Generaliste' }} — {{ now()->translatedFormat('l d F Y') }}</p>
</div>

{{-- STATISTIQUES --}}
<div class="grid grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
            <i class="fas fa-calendar-day text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-orange-500">{{ $rdvAujourdhui }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">RDV aujourd'hui</p>
        </div>
    </div>
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
        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-green-600">{{ $totalPatients }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">Patients</p>
        </div>
    </div>
</div>

{{-- RDV DU JOUR --}}
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
    <div class="p-6 border-b border-gray-50">
        <h2 class="text-sm font-black text-blue-900 uppercase">Rendez-vous du jour</h2>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($rdvsAujourdhui as $rdv)
        <div class="p-6 flex items-center justify-between hover:bg-gray-50/30 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-100">
                    {{ strtoupper(substr($rdv->patient->prenom ?? '', 0, 1)) }}{{ strtoupper(substr($rdv->patient->nom ?? '', 0, 1)) }}
                </div>
                <div>
                    <p class="font-black text-blue-900 text-sm uppercase">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</p>
                    <p class="text-[10px] text-gray-400 font-bold">{{ $rdv->patient->telephone }}</p>
                    @if($rdv->motif)
                        <p class="text-[10px] text-gray-500 mt-1">{{ $rdv->motif }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="bg-blue-50 text-blue-700 px-4 py-2 rounded-xl text-xs font-black">
                    {{ \Carbon\Carbon::parse($rdv->heure_rv)->format('H:i') }}
                </span>
                @if($rdv->statut === 'en_attente')
                    <span class="bg-orange-50 text-orange-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">En attente</span>
                @else
                    <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Termine</span>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-calendar-check text-4xl text-gray-200 mb-3"></i>
            <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun rendez-vous aujourd'hui</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
