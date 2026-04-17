@extends('layouts.master')

@section('content')
<div class="p-8 max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Feuille d'examen</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $feuille->date->format('d/m/Y') }} - Dr {{ $feuille->medecin->nom }} {{ $feuille->medecin->prenom }}</p>
        </div>
        <a href="{{ route('patient.examens.print', $feuille) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-md shadow-blue-100">
            <i class="fas fa-print"></i> Imprimer
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 p-8 space-y-4">
        @if($feuille->motif_clinique)
            <div class="text-sm text-gray-700"><strong class="uppercase text-[10px] tracking-widest text-gray-400 block mb-1">Motif clinique</strong>{{ $feuille->motif_clinique }}</div>
        @endif

        <div>
            <strong class="uppercase text-[10px] tracking-widest text-gray-400 block mb-2">Examens prescrits</strong>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[9px] font-black uppercase text-gray-400">
                    <tr><th class="p-3 text-left">Type</th><th class="p-3 text-left">Libelle</th><th class="p-3 text-left">Urgence</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($feuille->lignes as $l)
                    <tr>
                        <td class="p-3 uppercase font-bold text-blue-900 text-xs">{{ $l->type_examen }}</td>
                        <td class="p-3">{{ $l->libelle }}</td>
                        <td class="p-3">@if($l->urgence)<span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded-full font-bold">URGENT</span>@endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('patient.documents') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-blue-600"><i class="fas fa-arrow-left"></i> Retour aux documents</a>
    </div>
</div>
@endsection
