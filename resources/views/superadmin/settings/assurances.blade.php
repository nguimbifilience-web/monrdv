@extends('layouts.master')

@php
    $iconsAss = ['fa-shield-alt', 'fa-shield-heart', 'fa-file-invoice-dollar', 'fa-hand-holding-dollar', 'fa-building', 'fa-briefcase-medical', 'fa-umbrella', 'fa-coins', 'fa-credit-card', 'fa-receipt', 'fa-landmark', 'fa-handshake', 'fa-user-shield', 'fa-certificate', 'fa-file-contract', 'fa-money-check-dollar', 'fa-piggy-bank', 'fa-vault', 'fa-file-shield', 'fa-sack-dollar', 'fa-hospital-user', 'fa-wallet'];
@endphp

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Assurances globales</h1>
        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Référentiel partagé par les nouvelles cliniques</p>
    </div>
    <button type="button" onclick="toggleModal('modalAddAss')" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-5 md:px-6 py-3 rounded-2xl shadow-lg uppercase tracking-widest text-[10px]"><i class="fas fa-plus mr-2"></i>Ajouter</button>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg"><p class="text-xs font-bold">{{ session('success') }}</p></div>
@endif

<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4 md:p-6">
        @forelse($assurances as $ass)
            <div class="bg-gray-50 rounded-2xl p-4 {{ !$ass->is_active ? 'opacity-50' : '' }}">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                            <i class="fas {{ $ass->icone ?? 'fa-shield-alt' }} text-sm"></i>
                        </div>
                        <p class="font-black text-blue-900 text-xs uppercase truncate">{{ $ass->nom }}</p>
                    </div>
                    <div class="flex gap-1 shrink-0">
                        <button type="button" onclick="editAss({{ json_encode($ass) }})" class="w-7 h-7 bg-orange-50 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-all"><i class="fas fa-pen text-[9px]"></i></button>
                        <form action="{{ route('superadmin.settings.assurances.destroy', $ass) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="w-7 h-7 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all"><i class="fas fa-trash text-[9px]"></i></button>
                        </form>
                    </div>
                </div>
                @if($ass->type)<p class="text-[10px] text-gray-500">{{ $ass->type }}</p>@endif
                @if($ass->pays)<p class="text-[10px] text-gray-400"><i class="fas fa-map-marker-alt mr-1"></i>{{ $ass->pays }}</p>@endif
                @if($ass->contact)<p class="text-[10px] text-gray-400"><i class="fas fa-phone mr-1"></i>{{ $ass->contact }}</p>@endif
            </div>
        @empty
            <p class="col-span-full text-center py-12 text-gray-400 text-xs italic">Aucune assurance globale</p>
        @endforelse
    </div>
</div>

<div id="modalAddAss" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-blue-600 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Nouvelle assurance</h3>
            <button type="button" onclick="toggleModal('modalAddAss')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('superadmin.settings.assurances.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="nom" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Icône</label>
                <input type="text" name="icone" id="aa_icone" placeholder="fa-shield-alt" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0 mb-2">
                @include('superadmin.settings._icon-picker', ['inputId' => 'aa_icone', 'icons' => $iconsAss])
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Type</label>
                    <input type="text" name="type" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pays</label>
                    <input type="text" name="pays" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Contact</label>
                <input type="text" name="contact" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalAddAss')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-check mr-2"></i>Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditAss" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-orange-500 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Modifier</h3>
            <button type="button" onclick="toggleModal('modalEditAss')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="editAssForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="nom" id="ea_nom" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Icône</label>
                <input type="text" name="icone" id="ea_icone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0 mb-2">
                @include('superadmin.settings._icon-picker', ['inputId' => 'ea_icone', 'icons' => $iconsAss])
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Type</label>
                    <input type="text" name="type" id="ea_type" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pays</label>
                    <input type="text" name="pays" id="ea_pays" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Contact</label>
                <input type="text" name="contact" id="ea_contact" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <label class="flex items-center gap-2 text-xs font-bold text-blue-900">
                <input type="checkbox" name="is_active" id="ea_active" value="1" class="w-4 h-4 accent-orange-500">
                Active
            </label>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalEditAss')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
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

function editAss(a) {
    document.getElementById('ea_nom').value = a.nom;
    document.getElementById('ea_icone').value = a.icone || '';
    document.getElementById('ea_type').value = a.type || '';
    document.getElementById('ea_pays').value = a.pays || '';
    document.getElementById('ea_contact').value = a.contact || '';
    document.getElementById('ea_active').checked = !!a.is_active;
    document.getElementById('editAssForm').action = '/super-admin/parametres/assurances/' + a.id;
    toggleModal('modalEditAss');
}
</script>
@endsection
