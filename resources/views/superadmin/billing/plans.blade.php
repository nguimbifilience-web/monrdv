@extends('layouts.master')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <a href="{{ route('superadmin.billing.index') }}" class="text-[10px] font-black text-gray-400 hover:text-blue-600 uppercase tracking-widest"><i class="fas fa-arrow-left mr-1"></i>Facturation</a>
        <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Plans d'abonnement</h1>
    </div>
    <button type="button" onclick="toggleModal('modalAddPlan')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-5 md:px-6 py-3 rounded-2xl shadow-lg uppercase tracking-widest text-[10px]"><i class="fas fa-plus mr-2"></i>Nouveau plan</button>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
@endif
@if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
        @foreach($errors->all() as $e)<p class="text-xs">{{ $e }}</p>@endforeach
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
    @foreach($plans as $plan)
        <div class="bg-white rounded-3xl shadow-sm border-2 {{ $plan->is_active ? 'border-indigo-100' : 'border-gray-200' }} p-6 md:p-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-2xl font-black text-blue-900 uppercase italic">{{ $plan->name }}</h3>
                @if(!$plan->is_active)
                    <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-[9px] font-black uppercase">Désactivé</span>
                @endif
            </div>
            <p class="text-3xl font-black text-indigo-600 mb-1">{{ number_format($plan->price_monthly, 0, ',', ' ') }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-4">XAF / mois</p>
            <ul class="space-y-2 text-xs text-gray-600 mb-6">
                <li><i class="fas fa-user-md text-indigo-400 mr-2"></i>{{ $plan->max_medecins ?? '∞' }} médecins max</li>
                <li><i class="fas fa-calendar text-indigo-400 mr-2"></i>{{ $plan->max_rdv_monthly ? number_format($plan->max_rdv_monthly, 0, ',', ' ') : '∞' }} RDV/mois</li>
                <li><i class="fas {{ $plan->includes_insurance ? 'fa-check text-green-500' : 'fa-times text-gray-300' }} mr-2"></i>Gestion des assurances</li>
                <li><i class="fas fa-hospital text-indigo-400 mr-2"></i>{{ $plan->clinics_count }} clinique(s) abonnée(s)</li>
            </ul>
            @if($plan->description)
                <p class="text-[10px] text-gray-400 italic mb-4 border-t border-gray-100 pt-3">{{ $plan->description }}</p>
            @endif
            <div class="flex gap-2">
                <button type="button" onclick="editPlan({{ json_encode($plan) }})" class="flex-1 bg-orange-50 text-orange-500 rounded-xl py-2 text-[10px] font-black uppercase hover:bg-orange-500 hover:text-white transition-colors"><i class="fas fa-pen mr-1"></i>Modifier</button>
                <form action="{{ route('superadmin.billing.plans.destroy', $plan) }}" method="POST" class="flex-1" onsubmit="return confirm('Supprimer ce plan ?')">
                    @csrf @method('DELETE')
                    <button class="w-full bg-red-50 text-red-500 rounded-xl py-2 text-[10px] font-black uppercase hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-trash mr-1"></i>Supprimer</button>
                </form>
            </div>
        </div>
    @endforeach
</div>

{{-- Modal Add --}}
<div id="modalAddPlan" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-indigo-600 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Nouveau plan</h3>
            <button type="button" onclick="toggleModal('modalAddPlan')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('superadmin.billing.plans.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-indigo-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Slug *</label>
                    <input type="text" name="slug" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-indigo-500 focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Prix mensuel (XAF) *</label>
                <input type="number" name="price_monthly" required min="0" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-indigo-500 focus:ring-0">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Max médecins</label>
                    <input type="number" name="max_medecins" min="1" placeholder="∞" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-indigo-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Max RDV/mois</label>
                    <input type="number" name="max_rdv_monthly" min="1" placeholder="∞" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-indigo-500 focus:ring-0">
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs font-bold text-blue-900">
                <input type="checkbox" name="includes_insurance" value="1" class="w-4 h-4 accent-indigo-600">
                Gestion des assurances incluse
            </label>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-indigo-500 focus:ring-0"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalAddPlan')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-check mr-2"></i>Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditPlan" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-orange-500 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Modifier le plan</h3>
            <button type="button" onclick="toggleModal('modalEditPlan')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="editPlanForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="name" id="ep_name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Prix mensuel (XAF) *</label>
                <input type="number" name="price_monthly" id="ep_price" required min="0" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Max médecins</label>
                    <input type="number" name="max_medecins" id="ep_med" min="1" placeholder="∞" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Max RDV/mois</label>
                    <input type="number" name="max_rdv_monthly" id="ep_rdv" min="1" placeholder="∞" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs font-bold text-blue-900">
                <input type="checkbox" name="includes_insurance" id="ep_ins" value="1" class="w-4 h-4 accent-orange-500">
                Assurances incluses
            </label>
            <label class="flex items-center gap-2 text-xs font-bold text-blue-900">
                <input type="checkbox" name="is_active" id="ep_active" value="1" class="w-4 h-4 accent-orange-500">
                Plan actif
            </label>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Description</label>
                <textarea name="description" id="ep_desc" rows="2" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalEditPlan')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-save mr-2"></i>Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function editPlan(p) {
    document.getElementById('ep_name').value = p.name;
    document.getElementById('ep_price').value = p.price_monthly;
    document.getElementById('ep_med').value = p.max_medecins || '';
    document.getElementById('ep_rdv').value = p.max_rdv_monthly || '';
    document.getElementById('ep_ins').checked = !!p.includes_insurance;
    document.getElementById('ep_active').checked = !!p.is_active;
    document.getElementById('ep_desc').value = p.description || '';
    document.getElementById('editPlanForm').action = '/super-admin/facturation/plans/' + p.id;
    toggleModal('modalEditPlan');
}
</script>
@endsection
