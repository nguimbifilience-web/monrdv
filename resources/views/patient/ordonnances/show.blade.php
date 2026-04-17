@extends('layouts.master')

@section('content')
<div class="p-8 max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Ordonnance</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $ordonnance->date->format('d/m/Y') }} - Dr {{ $ordonnance->medecin->nom }} {{ $ordonnance->medecin->prenom }}</p>
        </div>
        <a href="{{ route('patient.ordonnances.print', $ordonnance) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-md shadow-blue-100">
            <i class="fas fa-print"></i> Imprimer
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 p-8 space-y-4">
        @if($ordonnance->notes_generales)
            <div class="text-sm text-gray-700"><strong class="uppercase text-[10px] tracking-widest text-gray-400 block mb-1">Notes</strong>{{ $ordonnance->notes_generales }}</div>
        @endif

        <div>
            <strong class="uppercase text-[10px] tracking-widest text-gray-400 block mb-2">Medicaments prescrits</strong>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[9px] font-black uppercase text-gray-400">
                    <tr><th class="p-3 text-left">Medicament</th><th class="p-3 text-left">Posologie</th><th class="p-3 text-left">Duree</th><th class="p-3 text-left">Quantite</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($ordonnance->lignes as $l)
                    <tr><td class="p-3 font-bold text-blue-900">{{ $l->medicament }}</td><td class="p-3">{{ $l->posologie }}</td><td class="p-3">{{ $l->duree }}</td><td class="p-3">{{ $l->quantite }}</td></tr>
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
