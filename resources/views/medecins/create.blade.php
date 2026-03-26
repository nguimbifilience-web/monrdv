@extends('layouts.master')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('medecins.index') }}" class="text-gray-500 hover:text-blue-600 transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
        <div class="bg-blue-600 py-6 text-center">
            <h4 class="text-white text-xl font-bold tracking-widest uppercase">Nouveau Praticien</h4>
        </div>
        
        <div class="p-10">
            <!-- AJOUT DU BLOC D'ERREURS POUR VOIR CE QUI BLOQUE -->
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
                    <p class="font-bold">Oups ! Il y a des erreurs :</p>
                    <ul class="mt-2 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medecins.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Nom du Médecin</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Ex: NDONG" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm border border-gray-200">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required placeholder="Ex: Paul" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm border border-gray-200">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Spécialité</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-blue-500">
                            <i class="fas fa-stethoscope"></i>
                        </span>
                        <!-- MODIFICATION ICI : On utilise un SELECT avec specialite_id -->
                        <select name="specialite_id" required 
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm border border-gray-200 appearance-none">
                            <option value="">Sélectionner une spécialité</option>
                            @foreach($specialites as $specialite)
                                <option value="{{ $specialite->id }}" {{ old('specialite_id') == $specialite->id ? 'selected' : '' }}>
                                    {{ $specialite->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Téléphone (Optionnel)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-blue-500">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="Ex: 074 00 00 00" 
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0 transition shadow-sm border border-gray-200">
                    </div>
                </div>

                <div class="flex">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-full shadow-lg transform hover:-translate-y-1 transition duration-200">
                        <i class="fas fa-check-circle mr-2"></i> ENREGISTRER LE PROFIL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection