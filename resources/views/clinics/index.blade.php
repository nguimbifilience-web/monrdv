@extends('layouts.master')

@section('content')

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Gestion des Cliniques</h1>
        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Panel Super Administrateur</p>
    </div>
    <button type="button" onclick="toggleModal('modalAddClinic')" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-5 md:px-6 py-3 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-[10px]">
        <i class="fas fa-plus mr-2"></i> Nouvelle Clinique
    </button>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
        <p class="text-xs font-black mb-1">Erreur(s) :</p>
        <ul class="text-xs list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Barre de filtres --}}
<form method="GET" action="{{ route('clinics.index') }}" class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 p-4 md:p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher..."
                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
        </div>
        <select name="status" onchange="this.form.submit()" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Tous les statuts</option>
            <option value="active" @selected(request('status') === 'active')>Actif</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspendu</option>
        </select>
        <select name="city" onchange="this.form.submit()" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Toutes les villes</option>
            @foreach($cities as $cityName)
                <option value="{{ $cityName }}" @selected(request('city') === $cityName)>{{ $cityName }}</option>
            @endforeach
        </select>
    </div>
    <div class="mt-3 flex justify-end gap-2">
        @if(request()->hasAny(['q', 'status', 'city']))
            <a href="{{ route('clinics.index') }}" class="border-2 border-gray-200 text-gray-600 font-black px-4 py-2 rounded-xl uppercase tracking-widest text-[10px] hover:bg-gray-50">
                <i class="fas fa-times mr-1"></i>Réinitialiser
            </a>
        @endif
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-5 py-2 rounded-xl uppercase tracking-widest text-[10px]">
            <i class="fas fa-search mr-1"></i>Filtrer
        </button>
    </div>
</form>

{{-- Liste des cliniques --}}
<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-[9px] font-black uppercase text-gray-500">
                    <th class="p-5">Nom</th>
                    <th class="p-5">Ville</th>
                    <th class="p-5 text-center">Statut</th>
                    <th class="p-5 text-center">Médecins</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clinics as $clinic)
                    @php
                        $statusBadge = $clinic->is_blocked
                            ? ['bg-red-100 text-red-600', 'Suspendu']
                            : ($clinic->is_active ? ['bg-green-100 text-green-600', 'Actif'] : ['bg-gray-100 text-gray-500', 'Inactif']);
                    @endphp
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                @if($clinic->logo_url)
                                    <img src="{{ $clinic->logo_url }}" class="w-10 h-10 rounded-xl object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-xs" style="background-color: {{ $clinic->getPrimaryColorOrDefault() }}">
                                        {{ strtoupper(substr($clinic->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-black text-blue-900 text-sm uppercase">{{ $clinic->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono">/c/{{ $clinic->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-xs font-bold text-gray-600">{{ $clinic->city ?? '—' }}</td>
                        <td class="p-5 text-center">
                            <span class="{{ $statusBadge[0] }} px-3 py-1 rounded-full text-[9px] font-black uppercase">{{ $statusBadge[1] }}</span>
                        </td>
                        <td class="p-5 text-center">
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black">{{ $clinic->medecins_count }}</span>
                        </td>
                        <td class="p-5">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('clinics.show', $clinic) }}"
                                   class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all" title="Voir">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                                <button type="button" data-clinic="{{ json_encode([
                                    'id' => $clinic->id,
                                    'name' => $clinic->name,
                                    'email' => $clinic->email,
                                    'phone' => $clinic->phone,
                                    'address' => $clinic->address,
                                    'city' => $clinic->city,
                                    'plan_id' => $clinic->plan_id,
                                    'primary_color' => $clinic->primary_color ?? '#1e3a8a',
                                    'secondary_color' => $clinic->secondary_color ?? '#f97316',
                                    'sidebar_text_color' => $clinic->sidebar_text_color ?? '#ffffff',
                                    'logo_url' => $clinic->logo_url,
                                    'subscription_expires_at' => $clinic->subscription_expires_at instanceof \DateTimeInterface ? $clinic->subscription_expires_at->format('Y-m-d') : null,
                                ], JSON_HEX_QUOT | JSON_HEX_APOS | JSON_INVALID_UTF8_SUBSTITUTE) }}" onclick="editClinic(JSON.parse(this.dataset.clinic))"
                                   class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all" title="Modifier">
                                    <i class="fas fa-pen text-[10px]"></i>
                                </button>
                                <button type="button" onclick="confirmDelete({{ $clinic->id }}, '{{ addslashes($clinic->name) }}')"
                                   class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all" title="Supprimer">
                                    <i class="fas fa-trash text-[10px]"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-16 text-center">
                            <i class="fas fa-hospital text-4xl text-gray-200 mb-3"></i>
                            <p class="text-gray-400 italic text-xs font-bold uppercase">Aucune clinique</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- Modal Ajout --}}
<div id="modalAddClinic" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-blue-600 py-4 md:py-5 px-6 md:px-8 rounded-t-2xl md:rounded-t-3xl flex items-center justify-between">
            <h3 class="text-white font-black text-base md:text-lg uppercase tracking-widest">Nouvelle Clinique</h3>
            <button type="button" onclick="toggleModal('modalAddClinic')" class="w-8 h-8 flex items-center justify-center text-white/60 hover:text-white rounded-lg"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('clinics.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom de la clinique *</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ville</label>
                    <input type="text" name="city" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Téléphone</label>
                    <input type="text" name="phone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse</label>
                <input type="text" name="address" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>

            <div class="border-t border-gray-100 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom admin</label>
                    <input type="text" name="admin_name" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email admin</label>
                    <input type="email" name="admin_email" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4">
                <button type="button" onclick="toggleModal('modalAddClinic')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px] py-3">Annuler</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-xl uppercase tracking-widest text-xs">
                    <i class="fas fa-check mr-2"></i>Créer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditClinic" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-orange-500 py-4 md:py-5 px-6 md:px-8 rounded-t-2xl md:rounded-t-3xl flex items-center justify-between">
            <h3 class="text-white font-black text-base md:text-lg uppercase tracking-widest">Modifier</h3>
            <button type="button" onclick="toggleModal('modalEditClinic')" class="w-8 h-8 flex items-center justify-center text-white/60 hover:text-white rounded-lg"><i class="fas fa-times"></i></button>
        </div>
        <form id="editClinicForm" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="name" id="edit_name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ville</label>
                    <input type="text" name="city" id="edit_city" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Téléphone</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse</label>
                <input type="text" name="address" id="edit_address" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" id="edit_email" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>

            {{-- Branding --}}
            <div class="border-t border-gray-100 pt-4">
                <p class="text-[10px] font-black text-blue-900 uppercase tracking-widest mb-3"><i class="fas fa-palette mr-1"></i> Branding</p>

                {{-- Logo actuel + upload --}}
                <div class="flex items-center gap-4 mb-3">
                    <img id="edit_logo_preview" src="" alt="" class="w-16 h-16 rounded-xl object-cover bg-gray-100 border-2 border-gray-200">
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Remplacer le logo</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-xs">
                        <button type="button" id="edit_remove_logo_btn" onclick="removeLogo()" class="mt-2 text-[10px] text-red-500 font-bold uppercase hover:underline hidden"><i class="fas fa-times mr-1"></i>Retirer le logo actuel</button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur 1</label>
                        <input type="color" name="primary_color" id="edit_primary_color" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur 2</label>
                        <input type="color" name="secondary_color" id="edit_secondary_color" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Texte sidebar</label>
                        <input type="color" name="sidebar_text_color" id="edit_sidebar_text_color" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                </div>
            </div>

            {{-- Abonnement --}}
            @if($plans->count())
            <div class="border-t border-gray-100 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Plan</label>
                    <select name="plan_id" id="edit_plan_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                        <option value="">Aucun</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->price_monthly, 0, ',', ' ') }} F/mois)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expiration</label>
                    <input type="date" name="subscription_expires_at" id="edit_subscription_expires_at" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
            </div>
            @endif

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-4">
                <button type="button" onclick="toggleModal('modalEditClinic')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px] py-3">Annuler</button>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black px-6 py-3 rounded-xl uppercase tracking-widest text-xs">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Suppression avec garde-fou (retaper nom) --}}
<div id="modalDeleteClinic" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-red-600 py-5 px-6 md:px-8 rounded-t-2xl md:rounded-t-3xl">
            <h3 class="text-white font-black text-base md:text-lg uppercase tracking-widest"><i class="fas fa-exclamation-triangle mr-2"></i>Supprimer</h3>
        </div>
        <form id="deleteClinicForm" method="POST" class="p-6 md:p-8 space-y-4">
            @csrf @method('DELETE')
            <p class="text-xs text-gray-600">
                Vous êtes sur le point de supprimer <span id="delete_clinic_name" class="font-black text-red-600"></span>.
                Toutes les données associées seront perdues définitivement.
            </p>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Retapez le nom de la clinique pour confirmer</label>
                <input type="text" name="confirm_name" required class="w-full bg-gray-50 border-2 border-red-200 rounded-xl p-3 font-bold text-red-700 text-sm focus:border-red-500 focus:ring-0">
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-3">
                <button type="button" onclick="toggleModal('modalDeleteClinic')" class="text-gray-400 hover:text-gray-700 font-bold uppercase text-[10px] py-3">Annuler</button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-black px-6 py-3 rounded-xl uppercase tracking-widest text-xs">
                    <i class="fas fa-trash mr-2"></i>Supprimer définitivement
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentClinicId = null;
function editClinic(c) {
    currentClinicId = c.id;
    document.getElementById('edit_name').value = c.name;
    document.getElementById('edit_email').value = c.email || '';
    document.getElementById('edit_phone').value = c.phone || '';
    document.getElementById('edit_address').value = c.address || '';
    document.getElementById('edit_city').value = c.city || '';
    document.getElementById('edit_primary_color').value = c.primary_color || '#1e3a8a';
    document.getElementById('edit_secondary_color').value = c.secondary_color || '#f97316';
    document.getElementById('edit_sidebar_text_color').value = c.sidebar_text_color || '#ffffff';

    const planField = document.getElementById('edit_plan_id');
    if (planField) planField.value = c.plan_id || '';
    const expField = document.getElementById('edit_subscription_expires_at');
    if (expField) expField.value = c.subscription_expires_at || '';

    const preview = document.getElementById('edit_logo_preview');
    const removeBtn = document.getElementById('edit_remove_logo_btn');
    if (c.logo_url) {
        preview.src = c.logo_url;
        preview.style.display = '';
        removeBtn.classList.remove('hidden');
    } else {
        preview.src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"><rect width="64" height="64" fill="%23f3f4f6"/><text x="32" y="38" font-size="12" text-anchor="middle" fill="%23a1a1aa">—</text></svg>';
        removeBtn.classList.add('hidden');
    }

    document.getElementById('editClinicForm').action = '/super-admin/cliniques/' + c.id;
    toggleModal('modalEditClinic');
}

function removeLogo() {
    if (!currentClinicId) return;
    if (!confirm('Supprimer le logo de cette clinique ?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/super-admin/cliniques/' + currentClinicId + '/logo';
    form.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    document.body.appendChild(form);
    form.submit();
}
function confirmDelete(id, name) {
    document.getElementById('delete_clinic_name').textContent = name;
    document.getElementById('deleteClinicForm').action = '/super-admin/cliniques/' + id;
    toggleModal('modalDeleteClinic');
}
</script>
@endsection
