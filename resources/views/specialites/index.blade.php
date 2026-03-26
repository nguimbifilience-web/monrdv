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
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Configuration médicale</p>
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
                        <th class="p-6">Désignation</th>
                        <th class="p-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($specialites as $s)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-6 font-black text-blue-900 uppercase text-xs">{{ $s->nom }}</td>
                        <td class="p-6 text-right space-x-3 text-gray-300">
                            <button onclick='openEditSpec(@json($s))' class="hover:text-cyan-400"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('specialites.destroy', $s->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="hover:text-red-400"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td class="p-20 text-center opacity-30 font-black italic text-xs uppercase">Vide</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL AJOUT --}}
<div id="modalAddSpec" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-sm p-8 shadow-2xl">
        <form action="{{ route('specialites.store') }}" method="POST">
            @csrf
            <h2 class="text-xl font-black text-blue-900 uppercase italic mb-6">Nouveau Pôle</h2>
            <input type="text" name="nom" class="w-full bg-gray-50 border-none rounded-xl p-4 font-bold" placeholder="Nom..." required>
            <div class="flex justify-end gap-4 mt-6 text-[10px] font-black uppercase">
                <button type="button" onclick="toggleModal('modalAddSpec')" class="text-gray-400">Annuler</button>
                <button type="submit" class="bg-cyan-400 text-white px-6 py-3 rounded-xl">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEditSpec" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-sm p-8 shadow-2xl">
        <form id="editSpecForm" method="POST">
            @csrf @method('PUT')
            <h2 class="text-xl font-black text-cyan-400 uppercase italic mb-6">Modifier</h2>
            <input type="text" name="nom" id="snom" class="w-full bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <div class="flex justify-end gap-4 mt-6 text-[10px] font-black uppercase">
                <button type="button" onclick="toggleModal('modalEditSpec')" class="text-gray-400">Fermer</button>
                <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) { document.getElementById(id).classList.toggle('hidden'); }
    function openEditSpec(s) {
        document.getElementById('editSpecForm').action = `/specialites/${s.id}`;
        document.getElementById('snom').value = s.nom;
        toggleModal('modalEditSpec');
    }
</script>
@endsection