@extends('layouts.master')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Utilisateurs globaux</h1>
        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Tous les utilisateurs, toutes cliniques confondues</p>
    </div>
    <button type="button" onclick="toggleModal('modalAddUser')" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-5 md:px-6 py-3 rounded-2xl shadow-lg uppercase tracking-widest text-[10px]">
        <i class="fas fa-plus mr-2"></i>Nouvel utilisateur
    </button>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
@endif

@if(session('reset_password'))
    @php $rp = json_decode(session('reset_password')); @endphp
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-xl">
        <p class="text-xs font-black text-yellow-700"><i class="fas fa-key mr-1"></i>Mot de passe réinitialisé</p>
        <p class="text-xs text-yellow-700 mt-1">{{ $rp->email }} → <code class="bg-yellow-100 px-2 py-1 rounded font-black">{{ $rp->password }}</code></p>
    </div>
@endif

{{-- Filtres --}}
<form method="GET" action="{{ route('superadmin.users.index') }}" class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 p-4 md:p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email..." class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
        </div>
        <select name="clinic_id" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Toutes les cliniques</option>
            <option value="none" @selected(request('clinic_id') === 'none')>— Non assigné —</option>
            @foreach($clinics as $c)
                <option value="{{ $c->id }}" @selected(request('clinic_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="role" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Tous les rôles</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin clinique</option>
            <option value="secretaire" @selected(request('role') === 'secretaire')>Secrétaire</option>
            <option value="medecin" @selected(request('role') === 'medecin')>Médecin</option>
            <option value="patient" @selected(request('role') === 'patient')>Patient</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 rounded-xl text-xs uppercase tracking-widest">
            <i class="fas fa-filter mr-1"></i>Filtrer
        </button>
    </div>
</form>

{{-- Tableau --}}
<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-[9px] font-black uppercase text-gray-500">
                    <th class="p-5">Nom</th>
                    <th class="p-5">Email</th>
                    <th class="p-5 text-center">Rôle</th>
                    <th class="p-5">Clinique</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-5 text-xs font-bold text-blue-900">{{ $user->name }}</td>
                        <td class="p-5 text-xs text-gray-500">{{ $user->email }}</td>
                        <td class="p-5 text-center">
                            @php
                                $roleColor = match($user->role) {
                                    'admin' => 'bg-orange-50 text-orange-600',
                                    'medecin' => 'bg-green-50 text-green-600',
                                    'patient' => 'bg-cyan-50 text-cyan-600',
                                    default => 'bg-blue-50 text-blue-600',
                                };
                            @endphp
                            <span class="{{ $roleColor }} px-2 py-0.5 rounded text-[9px] font-black uppercase">{{ $user->role }}</span>
                        </td>
                        <td class="p-5 text-xs text-gray-600">
                            {{ $user->clinic?->name ?? '—' }}
                        </td>
                        <td class="p-5">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" data-user="{{ json_encode(['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role,'clinic_id'=>$user->clinic_id], JSON_HEX_QUOT | JSON_HEX_APOS | JSON_INVALID_UTF8_SUBSTITUTE) }}" onclick="editUser(JSON.parse(this.dataset.user))"
                                        class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all" title="Modifier">
                                    <i class="fas fa-pen text-[10px]"></i>
                                </button>
                                <form action="{{ route('superadmin.users.reset', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Réinitialiser le mot de passe ?')">
                                    @csrf @method('PATCH')
                                    <button class="w-8 h-8 bg-yellow-50 text-yellow-500 rounded-lg flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-all" title="Reset mot de passe">
                                        <i class="fas fa-key text-[10px]"></i>
                                    </button>
                                </form>
                                <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce compte ?')">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all" title="Supprimer">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-16 text-center text-gray-400 text-xs italic">Aucun utilisateur</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<div class="mt-4">{{ $users->links() }}</div>

{{-- Modal Ajout --}}
<div id="modalAddUser" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-blue-600 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Nouvel utilisateur</h3>
            <button type="button" onclick="toggleModal('modalAddUser')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('superadmin.users.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email *</label>
                <input type="email" name="email" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Rôle *</label>
                <select name="role" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                    <option value="admin">Admin clinique</option>
                    <option value="secretaire">Secrétaire</option>
                    <option value="medecin">Médecin</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Clinique</label>
                <select name="clinic_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                    <option value="">— Non assigné —</option>
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalAddUser')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-check mr-2"></i>Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditUser" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-orange-500 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex justify-between items-center">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Modifier</h3>
            <button type="button" onclick="toggleModal('modalEditUser')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="editUserForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom *</label>
                <input type="text" name="name" id="eu_name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email *</label>
                <input type="email" name="email" id="eu_email" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Rôle *</label>
                <select name="role" id="eu_role" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                    <option value="admin">Admin clinique</option>
                    <option value="secretaire">Secrétaire</option>
                    <option value="medecin">Médecin</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Clinique</label>
                <select name="clinic_id" id="eu_clinic" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                    <option value="">— Non assigné —</option>
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalEditUser')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-save mr-2"></i>Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(u) {
    document.getElementById('eu_name').value = u.name;
    document.getElementById('eu_email').value = u.email;
    document.getElementById('eu_role').value = u.role;
    document.getElementById('eu_clinic').value = u.clinic_id || '';
    document.getElementById('editUserForm').action = '/super-admin/utilisateurs/' + u.id;
    toggleModal('modalEditUser');
}
</script>
@endsection
