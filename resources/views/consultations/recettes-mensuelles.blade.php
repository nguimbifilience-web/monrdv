@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Recettes Mensuelles</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Historique des recettes par jour</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('exports.recettes', ['mois' => $mois, 'annee' => $annee]) }}" class="bg-white border-2 border-gray-200 text-gray-700 px-6 py-3 rounded-xl font-black text-xs uppercase hover:border-blue-400 hover:text-blue-600 transition-all">
                <i class="fas fa-file-csv mr-2"></i> Exporter CSV
            </a>
            <a href="{{ route('consultations.index') }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
                <i class="fas fa-arrow-left"></i> Retour Historique
            </a>
        </div>
    </div>

    {{-- SELECTEUR MOIS / ANNEE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 mb-6">
        <form action="{{ route('consultations.recettes-mensuelles') }}" method="GET" class="flex items-center gap-4">
            <select name="mois" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[160px]">
                @foreach(['Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'] as $i => $nomMois)
                    <option value="{{ $i + 1 }}" {{ $mois == $i + 1 ? 'selected' : '' }}>{{ $nomMois }}</option>
                @endforeach
            </select>
            <select name="annee" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[120px]">
                @for($a = now()->year; $a >= now()->year - 3; $a--)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px]">
                <i class="fas fa-search mr-1"></i> Afficher
            </button>
        </form>
    </div>

    {{-- TOTAUX DU MOIS --}}
    <div class="grid grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                <i class="fas fa-file-medical text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-blue-900">{{ $totalMoisConsultations }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase">Consultations du mois</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500">
                <i class="fas fa-coins text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-green-600">{{ number_format($totalMoisRecettes, 0, ',', ' ') }} <span class="text-sm">F</span></p>
                <p class="text-[10px] font-black text-gray-400 uppercase">Total recettes du mois</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-cyan-50 rounded-xl flex items-center justify-center text-cyan-500">
                <i class="fas fa-shield-alt text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-cyan-600">{{ number_format($totalMoisAssurance, 0, ',', ' ') }} <span class="text-sm">F</span></p>
                <p class="text-[10px] font-black text-gray-400 uppercase">Total part assurances</p>
            </div>
        </div>
    </div>

    {{-- TABLEAU PAR JOUR --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Date</th>
                    <th class="p-5 text-center">Consultations</th>
                    <th class="p-5 text-center">Tarifs totaux</th>
                    <th class="p-5 text-center">Part assurance</th>
                    <th class="p-5 text-center">Recettes patients</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recettesParJour as $jour)
                <tr class="hover:bg-gray-50/30 transition-colors {{ \Carbon\Carbon::parse($jour->jour)->isToday() ? 'bg-orange-50/50' : '' }}">
                    <td class="p-5">
                        <div class="font-black text-blue-900 text-xs">
                            {{ \Carbon\Carbon::parse($jour->jour)->translatedFormat('l') }}
                        </div>
                        <div class="text-[10px] text-gray-400 font-bold">
                            {{ \Carbon\Carbon::parse($jour->jour)->format('d/m/Y') }}
                        </div>
                        @if(\Carbon\Carbon::parse($jour->jour)->isToday())
                            <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded text-[8px] font-black uppercase mt-1 inline-block">Aujourd'hui</span>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-xs font-black">{{ $jour->nb_consultations }}</span>
                    </td>
                    <td class="p-5 text-center">
                        <span class="font-black text-blue-900 text-xs">{{ number_format($jour->total_tarifs, 0, ',', ' ') }} F</span>
                    </td>
                    <td class="p-5 text-center">
                        @if($jour->part_assurance > 0)
                            <span class="bg-cyan-50 text-cyan-600 px-3 py-1 rounded-lg text-[10px] font-black">{{ number_format($jour->part_assurance, 0, ',', ' ') }} F</span>
                        @else
                            <span class="text-[10px] text-gray-300">--</span>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-xs font-black">{{ number_format($jour->recettes_patients, 0, ',', ' ') }} F</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-20 text-center">
                        <i class="fas fa-chart-bar text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucune recette pour ce mois</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($recettesParJour->count() > 0)
            <tfoot class="bg-blue-900">
                <tr class="text-white">
                    <td class="p-5 font-black text-xs uppercase">Total du mois</td>
                    <td class="p-5 text-center font-black text-xs">{{ $totalMoisConsultations }}</td>
                    <td class="p-5 text-center font-black text-xs">{{ number_format($recettesParJour->sum('total_tarifs'), 0, ',', ' ') }} F</td>
                    <td class="p-5 text-center font-black text-xs">{{ number_format($totalMoisAssurance, 0, ',', ' ') }} F</td>
                    <td class="p-5 text-center font-black text-xs">{{ number_format($totalMoisRecettes, 0, ',', ' ') }} F</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
