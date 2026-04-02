@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-10">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-black text-blue-900 uppercase italic">Spécialités</h1>
                <span class="bg-cyan-100 text-cyan-600 px-3 py-1 rounded-full text-[10px] font-black border border-cyan-200">
                    {{ $total }} PÔLES
                </span>
            </div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Configuration médicale & tarification</p>
        </div>
        <button onclick="toggleModal('modalAddSpec')" class="bg-cyan-400 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-lg">
            + Nouvelle Spécialité
        </button>
    </div>

    <div class="flex gap-8">
        <div class="w-1/4">
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-50">
                <form action="{{ route('specialites.index') }}" method="GET" class="space-y-6">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pôle..." class="w-full bg-gray-50 border-none rounded-xl p-4 text-xs font-bold text-blue-900">
                    <button type="submit" class="w-full bg-blue-900 text-white py-4 rounded-xl font-black uppercase text-[10px]">Filtrer</button>
                </form>
            </div>
        </div>

        <div class="w-3/4 bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr class="text-[9px] font-black uppercase text-gray-300 border-b border-gray-50">
                        <th class="p-5">Désignation</th>
                        <th class="p-5 text-center">Médecins</th>
                        <th class="p-5 text-center">RDV / mois</th>
                        <th class="p-5 text-center">Tarif consultation</th>
                        <th class="p-5 text-center">Tarifs assurés</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($specialites as $s)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-cyan-50 rounded-xl flex items-center justify-center text-cyan-500">
                                    <i class="fas fa-{{ $s->icone ?? 'stethoscope' }}"></i>
                                </div>
                                <span class="font-black text-blue-900 uppercase text-xs">{{ $s->nom }}</span>
                            </div>
                        </td>
                        <td class="p-5 text-center">
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[11px] font-black">
                                {{ $s->medecins_count }}
                            </span>
                        </td>
                        <td class="p-5 text-center">
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-[11px] font-black">
                                {{ $s->rdv_mois_count }}
                            </span>
                        </td>
                        {{-- Tarif non assuré --}}
                        <td class="p-5 text-center">
                            <div class="bg-red-50 rounded-xl p-2 border border-red-100 inline-block">
                                <span class="text-[9px] font-black text-red-400 uppercase block">Non assuré</span>
                                <span class="font-black text-red-600 text-sm">{{ number_format($s->tarif_consultation, 0, ',', ' ') }} F</span>
                            </div>
                        </td>
                        {{-- Tarifs par assurance --}}
                        <td class="p-5">
                            @if($s->tarif_consultation > 0 && $assurances->isNotEmpty())
                            <div class="space-y-1">
                                @foreach($assurances as $a)
                                    @php
                                        $partAssurance = round($s->tarif_consultation * $a->taux_couverture / 100);
                                        $partPatient = $s->tarif_consultation - $partAssurance;
                                    @endphp
                                    <div class="flex items-center justify-between bg-green-50 rounded-lg px-2 py-1 border border-green-100 text-[9px]">
                                        <span class="font-black text-gray-600 truncate max-w-[80px]" title="{{ $a->nom }}">{{ $a->nom }}</span>
                                        <div class="flex gap-2">
                                            <span class="text-green-600 font-black" title="Part assurance">
                                                <i class="fas fa-shield-alt mr-0.5"></i>{{ number_format($partAssurance, 0, ',', ' ') }}
                                            </span>
                                            <span class="text-blue-600 font-black" title="Part patient">
                                                <i class="fas fa-user mr-0.5"></i>{{ number_format($partPatient, 0, ',', ' ') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @else
                                <span class="text-[10px] text-gray-300 italic">—</span>
                            @endif
                        </td>
                        <td class="p-5 text-right space-x-3 text-gray-300">
                            <button onclick='openEditSpec(@json($s))' class="hover:text-cyan-400"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('specialites.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="hover:text-red-400"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-20 text-center opacity-30 font-black italic text-xs uppercase">Vide</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL AJOUT --}}
<div id="modalAddSpec" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl">
        <form action="{{ route('specialites.store') }}" method="POST">
            @csrf
            <h2 class="text-xl font-black text-blue-900 uppercase italic mb-6">Nouveau Pôle</h2>
            <div class="space-y-4">
                <input type="text" name="nom" class="w-full bg-gray-50 border-none rounded-xl p-4 font-bold" placeholder="Nom de la spécialité..." required>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Tarif consultation (FCFA)</label>
                    <input type="number" name="tarif_consultation" min="0" step="500" placeholder="Ex: 15000"
                        class="w-full bg-gray-50 border-none rounded-xl p-4 font-black text-blue-900" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Icône</label>
                    <div class="grid grid-cols-6 gap-2" id="iconGridAdd">
                        @foreach(['stethoscope','heartbeat','brain','bone','eye','tooth','lungs','baby','syringe','capsules','x-ray','microscope','user-md','hospital','notes-medical','procedures','diagnoses','hand-holding-medical','head-side-virus','allergies','bacterium','virus'] as $icon)
                        <label class="icon-option cursor-pointer">
                            <input type="radio" name="icone" value="{{ $icon }}" class="hidden peer">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 bg-gray-50 peer-checked:bg-cyan-400 peer-checked:text-white transition-all hover:bg-cyan-50">
                                <i class="fas fa-{{ $icon }}"></i>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-6 text-[10px] font-black uppercase">
                <button type="button" onclick="toggleModal('modalAddSpec')" class="text-gray-400">Annuler</button>
                <button type="submit" class="bg-cyan-400 text-white px-6 py-3 rounded-xl">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEditSpec" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl">
        <form id="editSpecForm" method="POST">
            @csrf @method('PUT')
            <h2 class="text-xl font-black text-cyan-400 uppercase italic mb-6">Modifier</h2>
            <div class="space-y-4">
                <input type="text" name="nom" id="snom" class="w-full bg-gray-50 border-none rounded-xl p-4 font-bold" required>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Tarif consultation (FCFA)</label>
                    <input type="number" name="tarif_consultation" id="starif" min="0" step="500"
                        class="w-full bg-gray-50 border-none rounded-xl p-4 font-black text-blue-900" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Icône</label>
                    <div class="grid grid-cols-6 gap-2" id="iconGridEdit">
                        @foreach(['stethoscope','heartbeat','brain','bone','eye','tooth','lungs','baby','syringe','capsules','x-ray','microscope','user-md','hospital','notes-medical','procedures','diagnoses','hand-holding-medical','head-side-virus','allergies','bacterium','virus'] as $icon)
                        <label class="icon-option cursor-pointer">
                            <input type="radio" name="icone" value="{{ $icon }}" class="hidden peer">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-400 bg-gray-50 peer-checked:bg-cyan-400 peer-checked:text-white transition-all hover:bg-cyan-50">
                                <i class="fas fa-{{ $icon }}"></i>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-6 text-[10px] font-black uppercase">
                <button type="button" onclick="toggleModal('modalEditSpec')" class="text-gray-400">Fermer</button>
                <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditSpec(s) {
        document.getElementById('editSpecForm').action = `/specialites/${s.id}`;
        document.getElementById('snom').value = s.nom;
        document.getElementById('starif').value = s.tarif_consultation || 0;

        document.querySelectorAll('#iconGridEdit input[type="radio"]').forEach(r => r.checked = false);
        if (s.icone) {
            const radio = document.querySelector(`#iconGridEdit input[value="${s.icone}"]`);
            if (radio) radio.checked = true;
        }

        toggleModal('modalEditSpec');
    }
</script>

{{-- FLASH --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
