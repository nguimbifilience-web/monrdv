@extends('layouts.master')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    {{-- Notification de succès --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-2xl shadow-sm flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-2xl font-black text-gray-800 uppercase tracking-tighter text-blue-900">Gestion des Médecins</h1>
            <p class="text-gray-400 italic text-sm">Annuaire officiel des praticiens de MonRDV.</p>
        </div>
        <a href="{{ route('medecins.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-2xl shadow-lg shadow-blue-200 transition-all flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Ajouter un médecin
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100 overflow-hidden border border-gray-50">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nom du Praticien</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Spécialité</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contact</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($medecins as $medecin)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white mr-4 font-bold shadow-md shadow-blue-100">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="font-bold text-gray-700">Dr. {{ strtoupper($medecin->nom) }} {{ $medecin->prenom }}</span>
                        </div>
                    </td>
                    <td class="p-5">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[11px] font-black uppercase tracking-wider">
                            {{-- CORRECTION ICI : On accède au nom de la relation --}}
                            {{ $medecin->specialite->nom ?? 'Généraliste' }}
                        </span>
                    </td>
                    <td class="p-5 text-sm text-gray-500 font-medium">
                        <div class="flex flex-col">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-envelope mr-2 text-gray-300 text-[10px]"></i>
                                {{ $medecin->email ?? 'contact@monrdv.ga' }}
                            </span>
                            <span class="text-[11px] text-blue-500 font-bold tracking-tight mt-1">
                                <i class="fas fa-phone-alt mr-2 text-gray-300 text-[10px]"></i>
                                {{ $medecin->telephone }}
                            </span>
                        </div>
                    </td>
                    <td class="p-5">
                        <div class="flex justify-center gap-2">
                            {{-- BOUTON MODIFIER --}}
                            <a href="{{ route('medecins.edit', $medecin->id) }}" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- BOUTON SUPPRIMER --}}
                            <form action="{{ route('medecins.destroy', $medecin->id) }}" method="POST" onsubmit="return confirm('Supprimer ce médecin définitivement ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border-none cursor-pointer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($medecins->isEmpty())
            <div class="py-20 text-center">
                <i class="fas fa-user-md text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-400 italic">Aucun médecin n'est encore enregistré.</p>
            </div>
        @endif
    </div>
</div>
@endsection