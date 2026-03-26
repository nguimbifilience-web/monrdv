@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-blue-100/50 overflow-hidden border border-gray-50">
        {{-- En-tête du formulaire --}}
        <div class="bg-blue-600 p-8 flex justify-between items-center">
            <div>
                <h2 class="text-white text-2xl font-black italic tracking-tighter text-blue-900">Mise à jour du Praticien</h2>
                <p class="text-blue-100 text-xs font-bold uppercase tracking-widest mt-1">Modification du profil de Dr. {{ $medecin->nom }}</p>
            </div>
            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-white text-3xl">
                <i class="fas fa-user-md"></i>
            </div>
        </div>
        
        <form action="{{ route('medecins.update', $medecin->id) }}" method="POST" class="p-10">
            @csrf
            @method('PUT') {{-- Indispensable pour la modification Laravel --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 text-blue-900">Nom du Médecin</label>
                    <input type="text" name="nom" value="{{ old('nom', $medecin->nom) }}" 
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-0 transition-all font-bold text-gray-700" required>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 text-blue-900">Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $medecin->prenom) }}" 
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 focus:ring-0 transition-all font-bold text-gray-700" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 text-blue-900">Spécialité médicale</label>
                    <input type="text" name="specialite" value="{{ old('specialite', $medecin->specialite) }}" 
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 font-bold text-gray-700" required>
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 text-blue-900">Numéro de téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $medecin->telephone) }}" 
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 bg-gray-50 focus:bg-white focus:border-blue-400 font-bold text-gray-700">
                </div>
            </div>

            <div class="mb-10">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 text-blue-900">Adresse Email Professionnelle</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email', $medecin->email) }}" 
                        class="w-full border-2 border-gray-50 rounded-2xl p-4 pl-12 bg-gray-50 focus:bg-white focus:border-blue-400 font-bold text-gray-700">
                </div>
            </div>

            <div class="flex items-center gap-6 pt-6 border-t border-gray-50">
                <button type="submit" class="bg-blue-600 text-white font-black px-10 py-4 rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all flex items-center uppercase tracking-widest text-xs">
                    <i class="fas fa-save mr-3"></i> Enregistrer les modifications
                </button>
                <a href="{{ route('medecins.index') }}" class="text-gray-400 font-bold hover:text-red-500 transition-colors uppercase tracking-widest text-[10px]">
                    Annuler et revenir
                </a>
            </div>
        </form>
    </div>
</div>
@endsection