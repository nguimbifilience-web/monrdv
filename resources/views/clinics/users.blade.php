@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('clinics.index') }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold uppercase tracking-widest mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Retour aux cliniques
            </a>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">{{ $clinic->name }}</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Gestion des utilisateurs</p>
        </div>
        <button onclick="toggleModal('modalAddUser')" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-3 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-[10px]">
            <i class="fas fa-plus mr-2"></i> Nouvel Utilisateur
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
    <script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 10000)</script>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
            <p class="text-2xl font-black text-gray-800">{{ $users->count() }}</p>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
            <p class="text-2xl font-black text-orange-500">{{ $users->where('role', 'admin')->count() }}</p>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Admins</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
            <p class="text-2xl font-black text-blue-500">{{ $users->where('role', 'secretaire')->count() }}</p>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Secretaires</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
            <p class="text-2xl font-black text-green-500">{{ $users->where('role', 'medecin')->count() }}</p>
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Medecins</p>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Utilisateur</th>
                    <th class="p-5">Email</th>
                    <th class="p-5">Mot de passe</th>
                    <th class="p-5 text-center">Role</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-md
                                {{ $user->role === 'admin' ? 'bg-orange-500' : ($user->role === 'medecin' ? 'bg-green-500' : ($user->role === 'patient' ? 'bg-cyan-500' : 'bg-blue-500')) }}">
                                @if($user->role === 'medecin')
                                    <i class="fas fa-user-md"></i>
                                @elseif($user->role === 'patient')
                                    <i class="fas fa-user"></i>
                                @elseif($user->role === 'admin')
                                    <i class="fas fa-user-shield"></i>
                                @else
                                    <i class="fas fa-user-tie"></i>
                                @endif
                            </div>
                            <p class="font-bold text-sm text-gray-800">{{ $user->name }}</p>
                        </div>
                    </td>
                    <td class="p-5">
                        <p class="text-xs font-mono text-gray-600">{{ $user->email }}</p>
                    </td>
                    <td class="p-5">
                        @if($user->plain_password)
                            <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded-lg text-gray-500">{{ $user->plain_password }}</span>
                        @else
                            <span class="text-[10px] text-gray-300 italic">modifie</span>
                        @endif
                    </td>
                    <td class="p-5 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                            {{ $user->role === 'admin' ? 'bg-orange-100 text-orange-600' : ($user->role === 'medecin' ? 'bg-green-100 text-green-600' : ($user->role === 'patient' ? 'bg-cyan-100 text-cyan-600' : 'bg-blue-100 text-blue-600')) }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}')"
                                class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all" title="Modifier">
                                <i class="fas fa-pen text-[10px]"></i>
                            </button>
                            <form action="{{ route('clinics.users.reset', [$clinic, $user]) }}" method="POST" onsubmit="return confirm('Reinitialiser le mot de passe ?')">
                                @csrf @method('PATCH')
                                <button class="w-8 h-8 bg-yellow-50 text-yellow-500 rounded-lg flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-all" title="Reset mot de passe">
                                    <i class="fas fa-key text-[10px]"></i>
                                </button>
                            </form>
                            <form action="{{ route('clinics.users.destroy', [$clinic, $user]) }}" method="POST" onsubmit="return confirm('Supprimer ce compte ?')">
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
                    <td colspan="5" class="p-20 text-center">
                        <i class="fas fa-users text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun utilisateur dans cette clinique</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Ajout Utilisateur --}}
<div id="modalAddUser" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl">
        <div class="bg-blue-600 py-5 px-8 rounded-t-[2rem]">
            <h3 class="text-white font-black text-lg uppercase tracking-widest">Nouvel Utilisateur</h3>
            <p class="text-blue-200 text-[10px] font-bold">{{ $clinic->name }}</p>
        </div>
        <form action="{{ route('clinics.users.store', $clinic) }}" method="POST" class="p-8 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom complet</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="Nom Prenom">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="email@clinique.ga">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Role</label>
                <select name="role" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                    <option value="admin">Administrateur</option>
                    <option value="secretaire">Secretaire</option>
                </select>
            </div>
            <p class="text-[10px] text-gray-400 italic">Le mot de passe sera genere automatiquement et affiche apres la creation.</p>
            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-8 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                    <i class="fas fa-check mr-2"></i> Creer
                </button>
                <button type="button" onclick="toggleModal('modalAddUser')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px]">Annuler</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Utilisateur --}}
<div id="modalEditUser" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl">
        <div class="bg-orange-500 py-5 px-8 rounded-t-[2rem]">
            <h3 class="text-white font-black text-lg uppercase tracking-widest">Modifier Utilisateur</h3>
        </div>
        <form id="editUserForm" method="POST" class="p-8 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom complet</label>
                <input type="text" name="name" id="edituser_name" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" id="edituser_email" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Role</label>
                <select name="role" id="edituser_role" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                    <option value="admin">Administrateur</option>
                    <option value="secretaire">Secretaire</option>
                    <option value="medecin">Medecin</option>
                    <option value="patient">Patient</option>
                </select>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-black px-8 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
                <button type="button" onclick="toggleModal('modalEditUser')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px]">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(id, name, email, role) {
    document.getElementById('edituser_name').value = name;
    document.getElementById('edituser_email').value = email;
    document.getElementById('edituser_role').value = role;
    document.getElementById('editUserForm').action = '/super-admin/cliniques/{{ $clinic->id }}/utilisateurs/' + id;
    toggleModal('modalEditUser');
}
</script>
@endsection
