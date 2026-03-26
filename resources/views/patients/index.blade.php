@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestion des Patients</h1>
            <p class="text-sm text-gray-500">Liste exhaustive et filtrage des dossiers patients</p>
        </div>
        <a href="{{ route('patients.create') }}" class="bg-[#00bcd4] hover:bg-cyan-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition duration-200 flex items-center gap-2">
            <i class="fas fa-plus"></i> Nouveau Patient
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b">
                    <i class="fas fa-sliders-h text-blue-500"></i>
                    <h3 class="font-bold text-gray-700 uppercase text-sm tracking-wider">Filtres</h3>
                </div>

                <form action="{{ route('patients.index') }}" method="GET" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Recherche rapide</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nom ou prénom..." 
                               class="w-full border-gray-200 rounded-lg bg-gray-50 text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Médecin Référent</label>
                        <select name="medecin_id" class="w-full border-gray-200 rounded-lg bg-gray-50 text-sm focus:ring-blue-500">
                            <option value="">Tous les médecins</option>
                            @foreach($medecins as $medecin)
                                <option value="{{ $medecin->id }}" {{ request('medecin_id') == $medecin->id ? 'selected' : '' }}>
                                    Dr. {{ $medecin->nom }} {{ $medecin->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Statut Assurance</label>
                        <div class="flex gap-4 mt-2">
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="est_assure" value="1" {{ request('est_assure') == '1' ? 'checked' : '' }} class="text-blue-500">
                                <span class="ml-2 text-gray-600">Oui</span>
                            </label>
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="est_assure" value="0" {{ request('est_assure') == '0' ? 'checked' : '' }} class="text-blue-500">
                                <span class="ml-2 text-gray-600">Non</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#00bcd4] text-white py-3 rounded-lg font-bold hover:bg-cyan-600 shadow-md transition">
                            Appliquer
                        </button>
                        
                        @if(request()->anyFilled(['search', 'medecin_id', 'est_assure']))
                            <a href="{{ route('patients.index') }}" class="block text-center text-xs text-red-500 mt-4 hover:underline">
                                <i class="fas fa-times"></i> Réinitialiser les filtres
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-400">
                        <tr>
                            <th class="p-4 text-xs font-bold uppercase tracking-wider">Patient</th>
                            <th class="p-4 text-xs font-bold uppercase tracking-wider">Contact</th>
                            <th class="p-4 text-xs font-bold uppercase tracking-wider">Quartier</th>
                            <th class="p-4 text-xs font-bold uppercase tracking-wider">Médecin</th>
                            <th class="p-4 text-xs font-bold uppercase tracking-wider text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($patients as $patient)
                            <tr class="hover:bg-blue-50/50 transition duration-150">
                                <td class="p-4 font-bold text-gray-800">
                                    {{ strtoupper($patient->nom) }} {{ $patient->prenom }}
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col text-gray-600">
                                        <span><i class="fas fa-phone text-xs mr-1 text-blue-400"></i> {{ $patient->telephone }}</span>
                                        <span class="text-xs italic">{{ $patient->email }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-gray-600">{{ $patient->quartier ?? 'Non spécifié' }}</td>
                                <td class="p-4">
                                    @if($patient->medecin)
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                                            Dr. {{ $patient->medecin->nom }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic">Aucun</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('patients.show', $patient) }}" class="text-blue-500 hover:text-blue-700 p-2 hover:bg-blue-100 rounded-full transition">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="text-orange-400 hover:text-orange-600 p-2 hover:bg-orange-100 rounded-full transition">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Supprimer ce patient ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-100 rounded-full transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400">
                                    <i class="fas fa-search-minus text-4xl mb-3"></i>
                                    <p>Aucun patient trouvé correspondant à vos critères.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    {{ $patients->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Message flash --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection