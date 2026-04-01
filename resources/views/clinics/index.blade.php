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

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-6 mb-8">
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
                    <p class="text-2xl font-black text-blue-900">{{ $clinics->where('is_active', true)->count() }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Actives</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600"><i class="fas fa-users text-xl"></i></div>
                <div>
                    <p class="text-2xl font-black text-blue-900">{{ $clinics->sum('users_count') }}</p>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Utilisateurs total</p>
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
                    <th class="p-5 text-center">Médecins</th>
                    <th class="p-5 text-center">Statut</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($clinics as $clinic)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <p class="font-black text-blue-900 text-sm uppercase">{{ $clinic->name }}</p>
                        <p class="text-[10px] text-gray-400 font-mono">/{{ $clinic->slug }}</p>
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
                        <form action="{{ route('clinics.toggle', $clinic) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $clinic->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}">
                                {{ $clinic->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('clinics.users', $clinic) }}"
                                class="w-8 h-8 bg-green-50 text-green-500 rounded-lg flex items-center justify-center hover:bg-green-500 hover:text-white transition-all" title="Utilisateurs">
                                <i class="fas fa-users text-[10px]"></i>
                            </a>
                            <button onclick="editClinic({{ $clinic->id }}, '{{ addslashes($clinic->name) }}', '{{ $clinic->email }}', '{{ $clinic->phone }}', '{{ addslashes($clinic->address) }}')"
                                class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-500 hover:text-white transition-all" title="Modifier">
                                <i class="fas fa-pen text-[10px]"></i>
                            </button>
                            <form action="{{ route('clinics.destroy', $clinic) }}" method="POST" onsubmit="return confirm('Supprimer cette clinique et toutes ses données ?')">
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
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl">
        <div class="bg-blue-600 py-5 px-8 rounded-t-[2rem]">
            <h3 class="text-white font-black text-lg uppercase tracking-widest">Nouvelle Clinique</h3>
        </div>
        <form action="{{ route('clinics.store') }}" method="POST" class="p-8 space-y-4">
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
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Téléphone</label>
                    <input type="text" name="phone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="074 00 00 00">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse</label>
                <input type="text" name="address" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0" placeholder="Quartier, Ville">
            </div>
            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-8 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                    <i class="fas fa-check mr-2"></i> Créer
                </button>
                <button type="button" onclick="toggleModal('modalAddClinic')" class="text-gray-400 hover:text-red-500 font-bold uppercase text-[10px]">Annuler</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditClinic" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl">
        <div class="bg-orange-500 py-5 px-8 rounded-t-[2rem]">
            <h3 class="text-white font-black text-lg uppercase tracking-widest">Modifier Clinique</h3>
        </div>
        <form id="editClinicForm" method="POST" class="p-8 space-y-4">
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
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Téléphone</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse</label>
                <input type="text" name="address" id="edit_address" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
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

<script>
function editClinic(id, name, email, phone, address) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email || '';
    document.getElementById('edit_phone').value = phone || '';
    document.getElementById('edit_address').value = address || '';
    document.getElementById('editClinicForm').action = '/super-admin/cliniques/' + id;
    toggleModal('modalEditClinic');
}
</script>
@endsection
