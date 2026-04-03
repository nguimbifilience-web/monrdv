@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Gestion des Cliniques</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Panel Super Administrateur</p>
        </div>
        <button onclick="toggleModal('modalAddClinic')" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-[10px]">
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

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600"><i class="fas fa-hospital text-xl"></i></div>
                <div>
                    <p class="text-2xl font-black text-blue-900">{{ $clinics->count() }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Cliniques</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600"><i class="fas fa-check-circle text-xl"></i></div>
                <div>
                    <p class="text-2xl font-black text-blue-900">{{ $clinics->where('is_active', true)->where('is_blocked', false)->count() }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Actives</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600"><i class="fas fa-ban text-xl"></i></div>
                <div>
                    <p class="text-2xl font-black text-blue-900">{{ $clinics->where('is_blocked', true)->count() }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Bloquees</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600"><i class="fas fa-users text-xl"></i></div>
                <div>
                    <p class="text-2xl font-black text-blue-900">{{ $clinics->sum('users_count') }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Utilisateurs</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Liste des cliniques --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Clinique</th>
                    <th class="p-5">Contact</th>
                    <th class="p-5 text-center">Utilisateurs</th>
                    <th class="p-5 text-center">Patients</th>
                    <th class="p-5 text-center">Medecins</th>
                    <th class="p-5 text-center">Statut</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($clinics as $clinic)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            @if($clinic->logo_url)
                                <img src="{{ $clinic->logo_url }}" class="w-10 h-10 rounded-xl object-cover">
                            @else
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-xs"
                                    style="background-color: {{ $clinic->getPrimaryColorOrDefault() }}">
                                    {{ strtoupper(substr($clinic->name, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <p class="font-black text-blue-900 text-sm uppercase">{{ $clinic->name }}</p>
                                <div class="flex items-center gap-1">
                                    <p class="text-[10px] text-gray-400 font-mono">/c/{{ $clinic->slug }}</p>
                                    <button onclick="copierLien('{{ route('clinic.portal', $clinic->slug) }}')" class="text-gray-300 hover:text-blue-500 transition-colors" title="Copier le lien d'acces">
                                        <i class="fas fa-copy text-[9px]"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="p-5">
                        <p class="text-xs font-bold text-gray-600">{{ $clinic->email ?? '—' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $clinic->phone ?? '—' }}</p>
                    </td>
                    <td class="p-5 text-center">
                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black">{{ $clinic->users_count }}</span>
                    </td>
                    <td class="p-5 text-center">
                        <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black">{{ $clinic->patients_count }}</span>
                    </td>
                    <td class="p-5 text-center">
                        <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-[10px] font-black">{{ $clinic->medecins_count }}</span>
                    </td>
                    <td class="p-5 text-center">
                        @if($clinic->is_blocked)
                            <span class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase" title="{{ $clinic->blocked_reason }}">
                                <i class="fas fa-ban mr-1"></i> Bloquee
                            </span>
                        @elseif($clinic->is_active)
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Active</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Inactive</span>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('clinics.users', $clinic) }}"
                                class="w-8 h-8 bg-green-50 text-green-500 rounded-lg flex items-center justify-center hover:bg-green-500 hover:text-white transition-all" title="Utilisateurs">
                                <i class="fas fa-users text-[10px]"></i>
                            </a>
                            <button onclick="editClinic({{ json_encode([
                                'id' => $clinic->id,
                                'name' => $clinic->name,
                                'email' => $clinic->email,
                                'phone' => $clinic->phone,
                                'address' => $clinic->address,
                                'primary_color' => $clinic->primary_color ?? '#1e3a8a',
                                'secondary_color' => $clinic->secondary_color ?? '#f97316',
                                'sidebar_text_color' => $clinic->sidebar_text_color ?? '#ffffff',
                                'logo_url' => $clinic->logo_url,
                                'subscription_expires_at' => $clinic->subscription_expires_at?->format('Y-m-d'),
                            ]) }})"
                                class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all" title="Modifier">
                                <i class="fas fa-pen text-[10px]"></i>
                            </button>

                            {{-- Toggle actif/inactif --}}
                            <form action="{{ route('clinics.toggle', $clinic) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button class="w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $clinic->is_active ? 'bg-yellow-50 text-yellow-500 hover:bg-yellow-500 hover:text-white' : 'bg-green-50 text-green-500 hover:bg-green-500 hover:text-white' }}"
                                    title="{{ $clinic->is_active ? 'Desactiver' : 'Activer' }}">
                                    <i class="fas {{ $clinic->is_active ? 'fa-pause' : 'fa-play' }} text-[10px]"></i>
                                </button>
                            </form>

                            {{-- Bloquer / Debloquer --}}
                            @if($clinic->is_blocked)
                                <form action="{{ route('clinics.unblock', $clinic) }}" method="POST" class="inline" onsubmit="return confirm('Debloquer cette clinique ?')">
                                    @csrf @method('PATCH')
                                    <button class="w-8 h-8 bg-emerald-50 text-emerald-500 rounded-lg flex items-center justify-center hover:bg-emerald-500 hover:text-white transition-all" title="Debloquer">
                                        <i class="fas fa-unlock text-[10px]"></i>
                                    </button>
                                </form>
                            @else
                                <button onclick="blockClinic({{ $clinic->id }}, '{{ addslashes($clinic->name) }}')"
                                    class="w-8 h-8 bg-red-50 text-red-400 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all" title="Bloquer">
                                    <i class="fas fa-lock text-[10px]"></i>
                                </button>
                            @endif

                            <form action="{{ route('clinics.destroy', $clinic) }}" method="POST" onsubmit="return confirm('Supprimer cette clinique et toutes ses donnees ?')">
                                @csrf @method('DELETE')
                                <button class="w-8 h-8 bg-red-50 text-red-400 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all" title="Supprimer">
                                    <i class="fas fa-trash text-[10px]"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-20 text-center">
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
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-blue-600 py-5 px-8 rounded-t-[2rem]">
            <h3 class="text-white font-black text-lg uppercase tracking-widest">Nouvelle Clinique</h3>
        </div>
        <form action="{{ route('clinics.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom de la clinique</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="Ex: Clinique Sainte Marie">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="contact@clinique.ga">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Telephone</label>
                    <input type="text" name="phone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="074 00 00 00">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse</label>
                <input type="text" name="address" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="Quartier, Ville">
            </div>

            {{-- Branding --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-3"><i class="fas fa-palette mr-1"></i> Apparence</p>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Logo (max 2Mo)</label>
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur principale</label>
                        <input type="color" name="primary_color" value="#1e3a8a" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur d'accent</label>
                        <input type="color" name="secondary_color" value="#f97316" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur texte sidebar</label>
                        <input type="color" name="sidebar_text_color" value="#ffffff" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                </div>
            </div>

            {{-- Admin de la clinique (optionnel) --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <p class="text-[10px] font-black text-orange-500 uppercase tracking-widest mb-3"><i class="fas fa-user-shield mr-1"></i> Administrateur (optionnel)</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom complet</label>
                        <input type="text" name="admin_name" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="Nom Prenom">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email admin</label>
                        <input type="email" name="admin_email" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="admin@clinique.ga">
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-8 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                    <i class="fas fa-check mr-2"></i> Creer
                </button>
                <button type="button" onclick="toggleModal('modalAddClinic')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px]">Annuler</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditClinic" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-orange-500 py-5 px-8 rounded-t-[2rem]">
            <h3 class="text-white font-black text-lg uppercase tracking-widest">Modifier Clinique</h3>
        </div>
        <form id="editClinicForm" method="POST" enctype="multipart/form-data" class="p-8 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom</label>
                <input type="text" name="name" id="edit_name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Telephone</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse</label>
                <input type="text" name="address" id="edit_address" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            </div>

            {{-- Branding --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-3"><i class="fas fa-palette mr-1"></i> Apparence</p>
                <div id="edit_logo_preview" class="mb-3 hidden">
                    <img id="edit_logo_img" src="" class="w-16 h-16 rounded-xl object-cover border-2 border-gray-100">
                    <button type="button" onclick="removeLogo()" class="text-[10px] text-red-500 font-bold mt-1 block"><i class="fas fa-times mr-1"></i>Supprimer le logo</button>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nouveau logo (max 512Ko)</label>
                    <input type="file" name="logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur principale</label>
                        <input type="color" name="primary_color" id="edit_primary_color" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur d'accent</label>
                        <input type="color" name="secondary_color" id="edit_secondary_color" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Couleur texte sidebar</label>
                        <input type="color" name="sidebar_text_color" id="edit_sidebar_text_color" class="w-full h-12 rounded-xl border-2 border-gray-100 cursor-pointer">
                    </div>
                </div>
            </div>

            {{-- Abonnement --}}
            <div class="border-t border-gray-100 pt-4 mt-4">
                <p class="text-[10px] font-black text-cyan-500 uppercase tracking-widest mb-3"><i class="fas fa-calendar mr-1"></i> Abonnement</p>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expiration</label>
                    <input type="date" name="subscription_expires_at" id="edit_subscription" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                </div>
            </div>

            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-black px-8 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
                <button type="button" onclick="toggleModal('modalEditClinic')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px]">Annuler</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Blocage --}}
<div id="modalBlockClinic" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl">
        <div class="bg-red-600 py-5 px-8 rounded-t-[2rem]">
            <h3 class="text-white font-black text-lg uppercase tracking-widest"><i class="fas fa-ban mr-2"></i>Bloquer la clinique</h3>
            <p class="text-red-200 text-[10px] font-bold mt-1" id="block_clinic_name"></p>
        </div>
        <form id="blockClinicForm" method="POST" class="p-8 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Motif du blocage</label>
                <textarea name="blocked_reason" required rows="3"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 text-sm focus:border-red-400 focus:ring-0"
                    placeholder="Ex: Non-paiement de l'abonnement mensuel..."></textarea>
            </div>
            <p class="text-[10px] text-red-400 italic">Les utilisateurs de cette clinique seront immediatement deconnectes et ne pourront plus acceder au systeme.</p>
            <div class="flex gap-4 pt-2">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-black px-8 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                    <i class="fas fa-lock mr-2"></i> Confirmer le blocage
                </button>
                <button type="button" onclick="toggleModal('modalBlockClinic')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px]">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentEditClinicId = null;

function copierLien(url) {
    navigator.clipboard.writeText(url).then(() => {
        const btn = event.currentTarget;
        const icon = btn.querySelector('i');
        icon.classList.replace('fa-copy', 'fa-check');
        btn.classList.add('text-green-500');
        setTimeout(() => {
            icon.classList.replace('fa-check', 'fa-copy');
            btn.classList.remove('text-green-500');
        }, 2000);
    });
}

function editClinic(c) {
    currentEditClinicId = c.id;
    document.getElementById('edit_name').value = c.name;
    document.getElementById('edit_email').value = c.email || '';
    document.getElementById('edit_phone').value = c.phone || '';
    document.getElementById('edit_address').value = c.address || '';
    document.getElementById('edit_primary_color').value = c.primary_color || '#1e3a8a';
    document.getElementById('edit_secondary_color').value = c.secondary_color || '#f97316';
    document.getElementById('edit_sidebar_text_color').value = c.sidebar_text_color || '#ffffff';
    document.getElementById('edit_subscription').value = c.subscription_expires_at || '';
    document.getElementById('editClinicForm').action = '/super-admin/cliniques/' + c.id;

    const preview = document.getElementById('edit_logo_preview');
    if (c.logo_url) {
        document.getElementById('edit_logo_img').src = c.logo_url;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }

    toggleModal('modalEditClinic');
}

function blockClinic(id, name) {
    document.getElementById('block_clinic_name').textContent = name;
    document.getElementById('blockClinicForm').action = '/super-admin/cliniques/' + id + '/block';
    toggleModal('modalBlockClinic');
}

function removeLogo() {
    if (!currentEditClinicId || !confirm('Supprimer le logo ?')) return;
    fetch('/super-admin/cliniques/' + currentEditClinicId + '/logo', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).then(() => {
        document.getElementById('edit_logo_preview').classList.add('hidden');
    }).catch(() => alert('Erreur lors de la suppression du logo.'));
}
</script>
@endsection
