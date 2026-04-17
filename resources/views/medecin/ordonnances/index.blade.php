@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Ordonnances</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Prescriptions que vous avez redigees</p>
        </div>
        <a href="{{ route('medecin.ordonnances.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-md shadow-blue-100">
            <i class="fas fa-plus"></i> Nouvelle ordonnance
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Date</th>
                    <th class="p-5">Patient</th>
                    <th class="p-5">Medicaments</th>
                    <th class="p-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($ordonnances as $o)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-5 text-xs font-bold text-blue-900">{{ $o->date->format('d/m/Y') }}</td>
                        <td class="p-5 text-xs font-black text-blue-900 uppercase">{{ $o->patient->nom }} {{ $o->patient->prenom }}</td>
                        <td class="p-5 text-xs text-gray-600">{{ $o->lignes->count() }} ligne(s)</td>
                        <td class="p-5 text-right">
                            <a href="{{ route('medecin.ordonnances.show', $o) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-widest"><i class="fas fa-eye"></i> Voir</a>
                            <a href="{{ route('medecin.ordonnances.print', $o) }}" target="_blank" class="ml-3 text-gray-500 hover:text-gray-700 text-xs font-bold uppercase tracking-widest"><i class="fas fa-print"></i> Imprimer</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-8 text-center text-xs text-gray-400 uppercase tracking-widest">Aucune ordonnance</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $ordonnances->links() }}</div>
</div>
@endsection
