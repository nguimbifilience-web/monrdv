@extends('layouts.master')

@section('content')
<div class="p-8">
    {{-- RETOUR + ACTIONS --}}
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('patients.index') }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <div class="flex gap-3">
            <a href="{{ route('patients.edit', $patient) }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-blue-700 transition-all">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <button onclick="window.print()" class="bg-gray-100 text-gray-600 px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-gray-200 transition-all">
                <i class="fas fa-print mr-1"></i> Imprimer
            </button>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
        {{-- COLONNE GAUCHE : INFOS PATIENT --}}
        <div class="col-span-1 space-y-6">
            {{-- Carte identité --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-8 text-center">
                <div class="w-20 h-20 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black mx-auto mb-4 shadow-lg shadow-blue-200">
                    {{ strtoupper(substr($patient->prenom, 0, 1)) }}{{ strtoupper(substr($patient->nom, 0, 1)) }}
                </div>
                <h2 class="text-xl font-black text-blue-900 uppercase">{{ $patient->nom }}</h2>
                <p class="text-sm text-gray-500 font-bold">{{ $patient->prenom }}</p>
                <p class="text-[10px] text-gray-300 font-black uppercase mt-1">ID #{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</p>

                <div class="mt-6 space-y-3 text-left">
                    <div class="flex items-center gap-3 text-xs">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-400"><i class="fas fa-phone"></i></div>
                        <span class="font-bold text-gray-700">{{ $patient->telephone }}</span>
                    </div>
                    @if($patient->email)
                    <div class="flex items-center gap-3 text-xs">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-400"><i class="fas fa-envelope"></i></div>
                        <span class="font-bold text-gray-600 lowercase">{{ $patient->email }}</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-3 text-xs">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-400"><i class="fas fa-map-marker-alt"></i></div>
                        <span class="font-bold text-gray-600">{{ $patient->quartier ?? 'Non renseigné' }}</span>
                    </div>
                </div>
            </div>

            {{-- Assurance --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-6">
                <h3 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-4">
                    <i class="fas fa-shield-alt mr-1"></i> Assurance
                </h3>
                @if($patient->est_assure && $patient->assurance)
                    <div class="bg-green-50 rounded-2xl p-4 border border-green-100">
                        <p class="font-black text-green-700 uppercase text-sm">{{ $patient->assurance->nom }}</p>
                        <div class="flex justify-between mt-2">
                            <span class="text-[10px] font-bold text-green-500 uppercase">
                                {{ $patient->assurance->type ?? 'N/A' }}
                            </span>
                            <span class="text-[10px] font-black text-green-600">
                                Taux : {{ $patient->assurance->taux_couverture }}%
                            </span>
                        </div>
                    </div>
                @else
                    <div class="bg-red-50 rounded-2xl p-4 border border-red-100 text-center">
                        <i class="fas fa-times-circle text-red-300 text-2xl mb-2"></i>
                        <p class="text-red-500 font-black text-xs uppercase">Non assuré</p>
                    </div>
                @endif
            </div>

            {{-- Médecin traitant --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-6">
                <h3 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-4">
                    <i class="fas fa-user-md mr-1"></i> Médecin traitant
                </h3>
                @if($patient->medecin)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <p class="font-black text-blue-900 text-sm">Dr. {{ $patient->medecin->nom }} {{ $patient->medecin->prenom }}</p>
                            <p class="text-[10px] text-indigo-500 font-bold uppercase">{{ $patient->medecin->specialite->nom ?? 'Généraliste' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400 italic text-xs text-center">Aucun médecin assigné</p>
                @endif
            </div>

            {{-- Médecins consultés --}}
            @if($medecinsConsultes->isNotEmpty())
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-6">
                <h3 class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-4">
                    <i class="fas fa-stethoscope mr-1"></i> Médecins consultés
                </h3>
                <div class="space-y-3">
                    @foreach($medecinsConsultes as $med)
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-500 text-xs">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-700 text-xs">Dr. {{ $med->nom }} {{ $med->prenom }}</p>
                            <p class="text-[9px] text-indigo-400 font-bold uppercase">{{ $med->specialite->nom ?? 'Généraliste' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE : HISTORIQUE + DOSSIER --}}
        <div class="col-span-2 space-y-6">
            {{-- Historique des RDV --}}
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-black text-blue-900 uppercase text-sm">
                        <i class="fas fa-calendar-alt text-cyan-400 mr-2"></i>
                        Historique des rendez-vous
                    </h3>
                    <span class="bg-cyan-50 text-cyan-600 px-3 py-1 rounded-full text-[10px] font-black">
                        {{ $patient->rendezvous->count() }} RDV
                    </span>
                </div>

                @if($patient->rendezvous->isNotEmpty())
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50">
                        <tr class="text-[9px] font-black uppercase text-gray-300">
                            <th class="p-4">Date</th>
                            <th class="p-4">Heure</th>
                            <th class="p-4">Spécialité</th>
                            <th class="p-4">Médecin</th>
                            <th class="p-4">Motif</th>
                            <th class="p-4 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($patient->rendezvous as $rdv)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-4 font-black text-blue-900 text-xs">
                                {{ \Carbon\Carbon::parse($rdv->date_rv)->format('d/m/Y') }}
                            </td>
                            <td class="p-4 text-xs font-bold text-gray-600">
                                {{ $rdv->heure_rv }}
                            </td>
                            <td class="p-4">
                                <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded-lg text-[10px] font-black uppercase">
                                    {{ $rdv->medecin->specialite->nom ?? 'Généraliste' }}
                                </span>
                            </td>
                            <td class="p-4 text-xs font-bold text-gray-600">
                                Dr. {{ $rdv->medecin->nom ?? 'N/A' }}
                            </td>
                            <td class="p-4 text-xs text-gray-500">{{ $rdv->motif ?? '-' }}</td>
                            <td class="p-4 text-center">
                                @if($rdv->statut === 'confirmé')
                                    <span class="bg-green-50 text-green-600 px-2 py-1 rounded-lg text-[10px] font-black">Confirmé</span>
                                @elseif($rdv->statut === 'annulé')
                                    <span class="bg-red-50 text-red-500 px-2 py-1 rounded-lg text-[10px] font-black">Annulé</span>
                                @elseif($rdv->statut === 'terminé')
                                    <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-lg text-[10px] font-black">Terminé</span>
                                @else
                                    <span class="bg-yellow-50 text-yellow-600 px-2 py-1 rounded-lg text-[10px] font-black">En attente</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-12 text-center">
                    <i class="fas fa-calendar-times text-4xl text-gray-200 mb-3"></i>
                    <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun rendez-vous enregistré</p>
                </div>
                @endif
            </div>

            {{-- Dossier médical --}}
            <div class="grid grid-cols-2 gap-6">
                <form action="{{ route('patients.update-notes', $patient) }}" method="POST" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-6">
                    @csrf @method('PATCH')
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-[10px] font-black text-gray-300 uppercase tracking-widest">
                            <i class="fas fa-notes-medical text-cyan-400 mr-1"></i> Notes médicales
                        </h3>
                        <button type="submit" class="bg-cyan-400 text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase hover:bg-cyan-500 transition-all">
                            <i class="fas fa-save mr-1"></i> Sauvegarder
                        </button>
                    </div>
                    <textarea name="notes_medicales" rows="6" placeholder="Écrire les notes médicales du patient..."
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 text-xs text-gray-600 leading-relaxed resize-none focus:ring-2 focus:ring-cyan-300 focus:bg-white transition-all">{{ $patient->notes_medicales }}</textarea>
                </form>

                <form action="{{ route('patients.update-notes', $patient) }}" method="POST" class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-6">
                    @csrf @method('PATCH')
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-[10px] font-black text-gray-300 uppercase tracking-widest">
                            <i class="fas fa-clipboard text-cyan-400 mr-1"></i> Observations
                        </h3>
                        <button type="submit" class="bg-cyan-400 text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase hover:bg-cyan-500 transition-all">
                            <i class="fas fa-save mr-1"></i> Sauvegarder
                        </button>
                    </div>
                    <textarea name="observations" rows="6" placeholder="Écrire les observations cliniques..."
                        class="w-full bg-gray-50 border-none rounded-2xl p-4 text-xs text-gray-600 leading-relaxed resize-none focus:ring-2 focus:ring-cyan-300 focus:bg-white transition-all">{{ $patient->observations }}</textarea>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
