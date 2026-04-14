@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Historique</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Consultations enregistrees</p>
        </div>
        <a href="{{ route('exports.consultations', request()->only(['mois', 'annee'])) }}" class="bg-white border-2 border-gray-200 text-gray-700 px-6 py-4 rounded-2xl font-black text-xs uppercase hover:border-blue-400 hover:text-blue-600 transition-all">
            <i class="fas fa-file-csv mr-2"></i> Exporter CSV
        </a>
    </div>

    {{-- LIEN RECETTES MENSUELLES --}}
    <div class="mb-6">
        <a href="{{ route('consultations.recettes-mensuelles') }}" class="inline-flex items-center gap-4 bg-white rounded-2xl shadow-sm border border-gray-50 px-8 py-5 hover:shadow-lg hover:border-orange-300 transition-all group">
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition">
                <i class="fas fa-chart-bar text-xl"></i>
            </div>
            <div>
                <p class="text-sm font-black text-blue-900 uppercase">Recettes du mois</p>
                <p class="text-[10px] font-black text-gray-400 uppercase">Voir l'historique des recettes</p>
            </div>
            <i class="fas fa-arrow-right text-gray-300 group-hover:text-orange-500 transition ml-4"></i>
        </a>
    </div>

    {{-- ONGLETS PAR MEDECIN --}}
    @php $currentMedecin = request('medecin_id', ''); @endphp

    <div class="flex gap-2 mb-6 flex-wrap">
        @php
            $tabParams = request()->except(['medecin_id', 'page']);
        @endphp
        <a href="{{ route('consultations.index', $tabParams) }}"
           class="flex items-center gap-2 px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all
           {{ !$currentMedecin ? 'bg-blue-500 text-white shadow-lg' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
            <i class="fas fa-list"></i>
            Tout
        </a>
        @foreach($medecins as $m)
            @php
                $mParams = array_merge($tabParams, ['medecin_id' => $m->id]);
                $isActive = $currentMedecin == $m->id;
            @endphp
            <a href="{{ route('consultations.index', $mParams) }}"
               class="flex items-center gap-2 px-5 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all
               {{ $isActive ? 'bg-green-500 text-white shadow-lg' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
                <i class="fas fa-user-md"></i>
                Dr. {{ $m->nom }}
            </a>
        @endforeach
    </div>

    {{-- FILTRES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 mb-6">
        <form action="{{ route('consultations.index') }}" method="GET" class="flex items-center gap-4">
            @if($currentMedecin)
                <input type="hidden" name="medecin_id" value="{{ $currentMedecin }}">
            @endif
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un patient..."
                    class="w-full bg-gray-50 border-none rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 placeholder-gray-400">
            </div>
            <div class="flex items-center gap-2">
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="bg-gray-50 border-none rounded-xl px-3 py-3 text-xs font-bold text-blue-900" placeholder="Du">
                <span class="text-gray-300 text-xs font-bold">→</span>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="bg-gray-50 border-none rounded-xl px-3 py-3 text-xs font-bold text-blue-900" placeholder="Au">
            </div>
            <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px]">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
            @if(request()->anyFilled(['search','medecin_id','date_debut','date_fin']))
                <a href="{{ route('consultations.index') }}" class="text-gray-400 hover:text-red-400 text-xs font-bold"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        @if($currentMedecin)
            @php $medecinActif = $medecins->firstWhere('id', $currentMedecin); @endphp
            @if($medecinActif)
            <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-green-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-user-md text-sm"></i>
                </div>
                <h2 class="font-black text-blue-900 text-sm uppercase tracking-wide">Dr. {{ $medecinActif->nom }} {{ $medecinActif->prenom }}</h2>
                <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded-lg text-[10px] font-black">{{ $consultations->total() }} consultations</span>
            </div>
            @endif
        @endif
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">N°</th>
                    <th class="p-5">Date</th>
                    <th class="p-5">Patient</th>
                    <th class="p-5">Medecin / Specialite</th>
                    <th class="p-5 text-center">Tarif</th>
                    <th class="p-5 text-center">Assurance</th>
                    <th class="p-5 text-center">Paye par patient</th>
                    <th class="p-5 text-center">Donne</th>
                    <th class="p-5 text-center">Rendu</th>
                    <th class="p-5 text-center">Ticket</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($consultations as $c)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <span class="text-[10px] font-black text-gray-400">#{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="p-5">
                        <div class="font-black text-blue-900 text-xs">{{ $c->created_at->format('d/m/Y') }}</div>
                        <div class="text-[10px] text-gray-400 font-bold">{{ $c->created_at->format('H:i') }}</div>
                    </td>
                    <td class="p-5">
                        <span class="font-black text-blue-900 text-xs uppercase">{{ $c->patient->nom ?? '' }} {{ $c->patient->prenom ?? '' }}</span>
                        @if($c->taux_couverture > 0 && $c->patient->assurance)
                            <div class="text-[9px] text-green-500 font-bold mt-1">
                                <i class="fas fa-shield-alt mr-1"></i>{{ $c->patient->assurance->nom }}
                            </div>
                        @endif
                    </td>
                    <td class="p-5">
                        <div class="text-xs font-bold text-gray-700">Dr. {{ $c->medecin->nom ?? '' }}</div>
                        <span class="bg-indigo-50 text-indigo-500 px-2 py-0.5 rounded text-[9px] font-black uppercase">
                            {{ $c->medecin->specialite->nom ?? 'Generaliste' }}
                        </span>
                    </td>
                    <td class="p-5 text-center">
                        <span class="font-black text-blue-900 text-xs">{{ number_format($c->montant_total, 0, ',', ' ') }} F</span>
                    </td>
                    <td class="p-5 text-center">
                        @if($c->taux_couverture > 0)
                            <div>
                                <span class="bg-green-50 text-green-600 px-2 py-1 rounded-lg text-[10px] font-black">
                                    {{ number_format($c->montant_assurance, 0, ',', ' ') }} F
                                </span>
                                <div class="text-[9px] text-gray-400 font-bold mt-1">{{ $c->taux_couverture }}%</div>
                            </div>
                        @else
                            <span class="text-[10px] text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-xs font-black">
                            {{ number_format($c->montant_patient, 0, ',', ' ') }} F
                        </span>
                    </td>
                    <td class="p-5 text-center">
                        <span class="text-xs font-bold text-gray-600">{{ number_format($c->montant_donne, 0, ',', ' ') }} F</span>
                    </td>
                    <td class="p-5 text-center">
                        @if($c->montant_rendu > 0)
                            <span class="bg-orange-50 text-orange-600 px-2 py-1 rounded-lg text-[10px] font-black">
                                {{ number_format($c->montant_rendu, 0, ',', ' ') }} F
                            </span>
                        @else
                            <span class="text-[10px] text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        <a href="{{ route('consultations.ticket', $c->id) }}" class="w-8 h-8 inline-flex items-center justify-center bg-cyan-50 text-cyan-500 rounded-lg hover:bg-cyan-500 hover:text-white transition-all" title="Imprimer le ticket">
                            <i class="fas fa-print text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="p-20 text-center">
                        <i class="fas fa-history text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucune consultation enregistree</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($consultations->hasPages())
        <div class="p-4 bg-gray-50/50 border-t border-gray-50">
            {{ $consultations->links() }}
        </div>
        @endif
    </div>
</div>

{{-- FLASH --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
