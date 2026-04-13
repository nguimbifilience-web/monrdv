@extends('layouts.master')

@php
    $iconsMed = ['fa-stethoscope', 'fa-heart', 'fa-heart-pulse', 'fa-tooth', 'fa-eye', 'fa-brain', 'fa-baby', 'fa-bone', 'fa-lungs', 'fa-user-doctor', 'fa-x-ray', 'fa-pills', 'fa-syringe', 'fa-microscope', 'fa-dna', 'fa-wheelchair', 'fa-ear-listen', 'fa-head-side-cough', 'fa-hand-holding-medical', 'fa-notes-medical', 'fa-kit-medical', 'fa-virus', 'fa-vial', 'fa-prescription-bottle-medical', 'fa-procedures', 'fa-person-pregnant', 'fa-allergies', 'fa-stomach'];
@endphp

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Spécialités globales</h1>
        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Référentiel partagé par les nouvelles cliniques</p>
    </div>
    <button type="button" onclick="toggleModal('modalAddSpec')" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-5 md:px-6 py-3 rounded-2xl shadow-lg uppercase tracking-widest text-[10px]"><i class="fas fa-plus mr-2"></i>Ajouter</button>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg"><p class="text-xs font-bold">{{ session('success') }}</p></div>
@endif

<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4 md:p-6">
        @forelse($specialites as $spec)
            <div class="bg-gray-50 rounded-2xl p-4 flex items-center gap-3 {{ !$spec->is_active ? 'opacity-50' : '' }}">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas {{ $spec->icone ?? 'fa-stethoscope' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-black text-blue-900 text-xs uppercase truncate">{{ $spec->nom }}</p>
                    @if($spec->description)<p class="text-[10px] text-gray-400 truncate">{{ $spec->description }}</p>@endif
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" onclick="editSpec({{ json_encode($spec) }})" class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-all"><i class="fas fa-pen text-[10px]"></i></button>
                    <form action="{{ route('superadmin.settings.specialites.destroy', $spec) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                        @csrf @method('DELETE')
                        <button class="w-8 h-8 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all"><i class="fas fa-trash text-[10px]"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center py-12 text-gray-400 text-xs italic">Aucune spécialité globale</p>
        @endforelse
    </div>
</div>

<div id="modalAddSpec" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-blue-600 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Nouvelle spécialité</h3>
            <button type="button" onclick="toggleModal('modalAddSpec')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('superadmin.settings.specialites.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="nom" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Icône</label>
                <input type="text" name="icone" id="as_icone" placeholder="fa-stethoscope" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0 mb-2">
                @include('superadmin.settings._icon-picker', ['inputId' => 'as_icone', 'icons' => $iconsMed])
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalAddSpec')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-check mr-2"></i>Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditSpec" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-orange-500 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Modifier</h3>
            <button type="button" onclick="toggleModal('modalEditSpec')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="editSpecForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="nom" id="es_nom" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Icône</label>
                <input type="text" name="icone" id="es_icone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0 mb-2">
                @include('superadmin.settings._icon-picker', ['inputId' => 'es_icone', 'icons' => $iconsMed])
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                <textarea name="description" id="es_desc" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0"></textarea>
            </div>
            <label class="flex items-center gap-2 text-xs font-bold text-blue-900">
                <input type="checkbox" name="is_active" id="es_active" value="1" class="w-4 h-4 accent-orange-500">
                Active
            </label>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalEditSpec')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-save mr-2"></i>Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.icon-picker button[data-icon]');
    if (!btn) return;
    const picker = btn.closest('.icon-picker');
    const inputId = picker.dataset.target;
    const input = document.getElementById(inputId);
    if (input) input.value = btn.dataset.icon;
    picker.querySelectorAll('button').forEach(b => b.classList.remove('bg-blue-500', 'text-white'));
    btn.classList.add('bg-blue-500', 'text-white');
});

function editSpec(s) {
    document.getElementById('es_nom').value = s.nom;
    document.getElementById('es_icone').value = s.icone || '';
    document.getElementById('es_desc').value = s.description || '';
    document.getElementById('es_active').checked = !!s.is_active;
    document.getElementById('editSpecForm').action = '/super-admin/parametres/specialites/' + s.id;
    toggleModal('modalEditSpec');
}
</script>
@endsection
