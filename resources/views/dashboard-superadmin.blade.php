@extends('layouts.master')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-blue-900 uppercase italic">Dashboard Super Admin</h1>
    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Vue globale de toutes les cliniques</p>
</div>

{{-- STATS GLOBALES --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-hospital text-red-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_clinics'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Cliniques</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-check-circle text-green-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['active_clinics'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Actives</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-users text-blue-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_users'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Utilisateurs</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-user-injured text-cyan-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_patients'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Patients</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-user-md text-purple-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_medecins'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Medecins</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-calendar-check text-orange-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_rdv_today'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">RDV aujourd'hui</p>
    </div>
</div>

{{-- TABLEAU DES CLINIQUES --}}
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
    <div class="p-8 border-b border-gray-50">
        <h2 class="text-lg font-black text-blue-900 uppercase">Cliniques</h2>
    </div>
    <table class="w-full text-left">
        <thead class="bg-gray-50/50 border-b border-gray-50">
            <tr class="text-[9px] font-black uppercase text-gray-300">
                <th class="p-5">Clinique</th>
                <th class="p-5 text-center">Statut</th>
                <th class="p-5 text-center">Utilisateurs</th>
                <th class="p-5 text-center">Patients</th>
                <th class="p-5 text-center">Medecins</th>
                <th class="p-5 text-center">RDV</th>
                <th class="p-5 text-center">Consultations</th>
                <th class="p-5 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($clinics as $clinic)
            <tr class="hover:bg-gray-50/30 transition-colors">
                <td class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br {{ $clinic->is_active ? 'from-blue-500 to-blue-600' : 'from-gray-400 to-gray-500' }} rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-hospital text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-800">{{ $clinic->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $clinic->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="p-5 text-center">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                        {{ $clinic->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                        {{ $clinic->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->users_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->patients_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->medecins_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->rendezvous_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->consultations_count }}</td>
                <td class="p-5 text-center">
                    <a href="{{ route('clinics.index') }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold">
                        <i class="fas fa-cog"></i> Gerer
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- SECTION GESTION DES COMPTES PAR CLINIQUE --}}
<div class="mt-10">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-black text-blue-900 uppercase">Comptes par clinique</h2>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $comptes->count() }} compte(s) sur {{ $comptes->pluck('clinic_id')->unique()->count() }} clinique(s)</p>
        </div>
        <a href="{{ route('clinics.index') }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold uppercase">
            <i class="fas fa-cog mr-1"></i> Gerer les cliniques
        </a>
    </div>

    @if(session('reset_password'))
    @php $resetData = json_decode(session('reset_password')); @endphp
    <div class="mb-4 p-5 bg-yellow-50 border-2 border-yellow-300 rounded-2xl">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-key text-yellow-500"></i>
            <span class="font-black text-yellow-700 text-sm uppercase">Mot de passe reinitialise</span>
        </div>
        <div class="bg-white rounded-xl p-4 space-y-2">
            <p class="text-xs text-gray-500"><span class="font-bold">Nom :</span> {{ $resetData->name }}</p>
            <p class="text-xs text-gray-500"><span class="font-bold">Email :</span> {{ $resetData->email }}</p>
            <div class="flex items-center gap-3 mt-2">
                <span class="font-bold text-xs text-gray-500">Mot de passe :</span>
                <code class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-xl font-black text-lg tracking-widest" id="saResetPwd">{{ $resetData->password }}</code>
                <button onclick="navigator.clipboard.writeText(document.getElementById('saResetPwd').textContent); this.innerHTML='<i class=\'fas fa-check text-green-500\'></i> Copie !'; setTimeout(() => this.innerHTML='<i class=\'fas fa-copy\'></i> Copier', 2000)"
                    class="bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-[10px] font-black uppercase hover:bg-yellow-300 transition-all">
                    <i class="fas fa-copy"></i> Copier
                </button>
            </div>
        </div>
    </div>
    @endif

    @php $comptesParClinic = $comptes->groupBy('clinic_id'); @endphp

    <div class="space-y-4">
        @foreach($comptesParClinic as $clinicId => $clinicComptes)
        @php $clinicInfo = $clinicComptes->first()->clinic; @endphp
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden">
            {{-- Header clinique (cliquable pour deplier) --}}
            <button onclick="document.getElementById('clinic-comptes-{{ $clinicId }}').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')"
                class="w-full flex items-center justify-between p-5 hover:bg-gray-50/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    @if($clinicInfo?->logo_url)
                        <img src="{{ $clinicInfo->logo_url }}" class="w-10 h-10 rounded-xl object-cover">
                    @else
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-xs"
                            style="background-color: {{ $clinicInfo?->getPrimaryColorOrDefault() ?? '#1e3a8a' }}">
                            {{ strtoupper(substr($clinicInfo?->name ?? '?', 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-black text-blue-900 text-sm uppercase">{{ $clinicInfo?->name ?? 'Clinique inconnue' }}</p>
                        <p class="text-[10px] text-gray-400">{{ $clinicComptes->count() }} compte(s) —
                            {{ $clinicComptes->where('role', 'admin')->count() }} admin,
                            {{ $clinicComptes->where('role', 'secretaire')->count() }} secr.,
                            {{ $clinicComptes->where('role', 'medecin')->count() }} med.,
                            {{ $clinicComptes->where('role', 'patient')->count() }} pat.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('clinics.users', $clinicId) }}" class="text-blue-500 hover:text-blue-700 text-[10px] font-bold uppercase" onclick="event.stopPropagation()">
                        <i class="fas fa-external-link-alt mr-1"></i> Gerer
                    </a>
                    <i class="fas fa-chevron-down chevron text-gray-300 transition-transform duration-200"></i>
                </div>
            </button>

            {{-- Tableau des comptes (deplie par defaut) --}}
            <div id="clinic-comptes-{{ $clinicId }}">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-y border-gray-50">
                        <tr class="text-[9px] font-black uppercase text-gray-300">
                            <th class="px-5 py-3">Utilisateur</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3 text-center">Role</th>
                            <th class="px-5 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($clinicComptes as $compte)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-5 py-3">
                                <span class="font-bold text-blue-900 text-xs">{{ $compte->name }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs text-gray-600">{{ $compte->email }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-1 rounded-lg text-[10px] font-black uppercase
                                    {{ $compte->role === 'admin' ? 'bg-orange-50 text-orange-600' : ($compte->role === 'medecin' ? 'bg-green-50 text-green-600' : ($compte->role === 'patient' ? 'bg-cyan-50 text-cyan-600' : 'bg-blue-50 text-blue-600')) }}">
                                    {{ $compte->role }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <button onclick="ouvrirResetSA({{ $clinicId }}, {{ $compte->id }}, '{{ addslashes($compte->name) }}')"
                                    class="w-8 h-8 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-all mx-auto" title="Reinitialiser mot de passe">
                                    <i class="fas fa-key text-[10px]"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- MODAL RESET SUPER ADMIN --}}
<div id="modalResetSA" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-black text-yellow-600 uppercase"><i class="fas fa-key mr-2"></i>Reinitialiser</h2>
            <button onclick="toggleModal('modalResetSA')" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-400 rounded-xl hover:bg-red-50 hover:text-red-400 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-sm font-bold text-gray-600 mb-4">Compte : <span id="saResetName" class="text-blue-900"></span></p>
        <form id="formResetSA" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nouveau mot de passe <span class="text-gray-300">(laisser vide = auto)</span></label>
                <input type="text" name="new_password"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-yellow-400 focus:ring-0 transition-all"
                    placeholder="Min. 8 caracteres ou laisser vide">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleModal('modalResetSA')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="bg-yellow-500 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-yellow-600 transition-all">
                    <i class="fas fa-key mr-1"></i> Reinitialiser
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function ouvrirResetSA(clinicId, userId, name) {
    document.getElementById('saResetName').textContent = name;
    document.getElementById('formResetSA').action = '/super-admin/cliniques/' + clinicId + '/utilisateurs/' + userId + '/reset';
    toggleModal('modalResetSA');
}
</script>
@endsection
