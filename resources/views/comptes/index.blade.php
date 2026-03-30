@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Comptes</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Gestion des identifiants de connexion</p>
        </div>
    </div>

    {{-- MESSAGES --}}
    @if(session('success'))
    <div id="flash-msg" class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i>
        <span class="text-green-700 text-sm font-black">{{ session('success') }}</span>
    </div>
    <script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 10000)</script>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl flex items-center gap-2">
        <i class="fas fa-exclamation-triangle text-red-500"></i>
        <span class="text-red-700 text-sm font-black">{{ session('error') }}</span>
    </div>
    @endif

    {{-- TABLEAU --}}
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
                @foreach($comptes as $compte)
                <tr class="hover:bg-gray-50/30 transition-colors" id="row-{{ $compte->id }}">
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold shadow-md
                                {{ $compte->role === 'admin' ? 'bg-orange-500' : ($compte->role === 'medecin' ? 'bg-green-500' : ($compte->role === 'patient' ? 'bg-cyan-500' : 'bg-blue-500')) }}">
                                @if($compte->role === 'medecin')
                                    <i class="fas fa-user-md"></i>
                                @elseif($compte->role === 'patient')
                                    <i class="fas fa-user"></i>
                                @elseif($compte->role === 'admin')
                                    <i class="fas fa-crown"></i>
                                @else
                                    <i class="fas fa-headset"></i>
                                @endif
                            </div>
                            <div>
                                <span class="font-black text-blue-900 text-xs" id="name-display-{{ $compte->id }}">{{ $compte->name }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-5">
                        <span class="text-xs font-bold text-gray-600">{{ $compte->email }}</span>
                    </td>
                    <td class="p-5">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded-lg text-gray-600" id="pwd-mask-{{ $compte->id }}">••••••••</span>
                            <span class="text-xs font-mono bg-green-50 px-2 py-1 rounded-lg text-green-700 font-bold hidden" id="pwd-clear-{{ $compte->id }}">{{ $compte->plain_password ?? 'non defini' }}</span>
                            <button type="button" onclick="togglePwd({{ $compte->id }})" class="text-gray-400 hover:text-blue-600 transition" title="Voir/masquer">
                                <i class="fas fa-eye text-[10px]" id="eye-{{ $compte->id }}"></i>
                            </button>
                        </div>
                    </td>
                    <td class="p-5 text-center">
                        @if($compte->role === 'admin')
                            <span class="bg-orange-50 text-orange-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Admin</span>
                        @elseif($compte->role === 'secretaire')
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Secretaire</span>
                        @elseif($compte->role === 'medecin')
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Medecin</span>
                        @else
                            <span class="bg-cyan-50 text-cyan-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Patient</span>
                        @endif
                    </td>
                    <td class="p-5">
                        <div class="flex justify-center gap-2">
                            {{-- Modifier --}}
                            <button onclick="ouvrirModif({{ $compte->id }}, '{{ addslashes($compte->name) }}', '{{ $compte->email }}', '{{ addslashes($compte->plain_password) }}')"
                                class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Réinitialiser mot de passe --}}
                            <form action="{{ route('comptes.reset-password', $compte->id) }}" method="POST" onsubmit="return confirm('Réinitialiser le mot de passe de {{ addslashes($compte->name) }} ?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-yellow-50 text-yellow-600 rounded-xl hover:bg-yellow-500 hover:text-white transition-all" title="Reinitialiser mot de passe">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>

                            {{-- Supprimer (pas soi-même) --}}
                            @if($compte->id !== auth()->id())
                            <form action="{{ route('comptes.destroy', $compte->id) }}" method="POST" onsubmit="return confirm('Supprimer le compte de {{ addslashes($compte->name) }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL MODIFIER --}}
<div id="modalModif" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-black text-blue-900 uppercase">Modifier le compte</h2>
            <button onclick="fermerModif()" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-400 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formModif" method="POST">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom</label>
                <input type="text" name="name" id="modif_name" required
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all">
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" id="modif_email" required
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all">
            </div>

            {{-- Ancien mot de passe (lecture seule) --}}
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Mot de passe actuel</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="modif_old_password" readonly
                        class="flex-1 bg-yellow-50 border-2 border-yellow-200 rounded-2xl px-4 py-3 font-bold text-yellow-700 text-sm">
                    <button type="button" onclick="copierMdp()" class="w-10 h-10 flex items-center justify-center bg-yellow-50 text-yellow-600 rounded-xl hover:bg-yellow-500 hover:text-white transition-all" title="Copier">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            {{-- Nouveau mot de passe --}}
            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nouveau mot de passe <span class="text-gray-300">(laisser vide pour garder l'actuel)</span></label>
                <input type="text" name="password" id="modif_password" placeholder="Nouveau mot de passe..."
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all" oninput="toggleConfirmation()">
            </div>

            {{-- Confirmation --}}
            <div class="mb-6" id="bloc_confirmation" style="display: none;">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Confirmer le nouveau mot de passe</label>
                <input type="text" name="password_confirmation" id="modif_password_confirm" placeholder="Retapez le mot de passe..."
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all">
                <p id="confirm_msg" class="text-[10px] font-bold mt-1 hidden"></p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="fermerModif()" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-blue-800 transition-all">
                    <i class="fas fa-save mr-1"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrirModif(id, name, email, plainPwd) {
    document.getElementById('modif_name').value = name;
    document.getElementById('modif_email').value = email;
    document.getElementById('modif_old_password').value = plainPwd || '••••••••';
    document.getElementById('modif_password').value = '';
    document.getElementById('modif_password_confirm').value = '';
    document.getElementById('bloc_confirmation').style.display = 'none';
    document.getElementById('formModif').action = '/comptes/' + id;
    document.getElementById('modalModif').classList.remove('hidden');
}

function fermerModif() {
    document.getElementById('modalModif').classList.add('hidden');
}

function toggleConfirmation() {
    const pwd = document.getElementById('modif_password').value;
    document.getElementById('bloc_confirmation').style.display = pwd.length > 0 ? 'block' : 'none';
}

function copierMdp() {
    const pwd = document.getElementById('modif_old_password').value;
    navigator.clipboard.writeText(pwd);
    alert('Mot de passe copié !');
}

function togglePwd(id) {
    const mask = document.getElementById('pwd-mask-' + id);
    const clear = document.getElementById('pwd-clear-' + id);
    const eye = document.getElementById('eye-' + id);

    if (clear.classList.contains('hidden')) {
        clear.classList.remove('hidden');
        mask.classList.add('hidden');
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        clear.classList.add('hidden');
        mask.classList.remove('hidden');
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection
