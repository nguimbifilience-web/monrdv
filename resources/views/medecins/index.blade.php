@extends('layouts.master')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    @if(session('success'))
        <div id="flash-msg" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-2xl shadow-sm flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        <script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-black text-blue-900 uppercase tracking-tighter">Gestion des Médecins</h1>
            <p class="text-gray-400 italic text-sm">Annuaire officiel des praticiens de MonRDV.</p>
        </div>
        <a href="{{ route('medecins.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-2xl shadow-lg shadow-blue-200 transition-all flex items-center">
            <i class="fas fa-plus-circle mr-2"></i> Ajouter un médecin
        </a>
    </div>

    {{-- BARRE DE FILTRES AJAX --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                <input type="text" id="searchMedecin" placeholder="Rechercher en temps réel (nom, prénom, téléphone)..."
                    class="w-full bg-gray-50 border-none rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 placeholder-gray-400"
                    value="{{ request('search') }}" oninput="filtrerMedecins()">
            </div>
            <select id="filterSpecialite" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[200px]" onchange="filtrerMedecins()">
                <option value="">Toutes les spécialités</option>
                @foreach($specialites as $spec)
                    <option value="{{ $spec->id }}">{{ $spec->nom }}</option>
                @endforeach
            </select>
            <span id="countResult" class="text-[10px] font-black text-gray-400 uppercase whitespace-nowrap"></span>
        </div>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-100 overflow-hidden border border-gray-50">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Praticien</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Spécialité</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Contact</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tarif/h</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Heures/mois</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Montant total</th>
                    <th class="p-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="medecinTableBody" class="divide-y divide-gray-50">
                @foreach($medecins as $medecin)
                @php $montantTotal = $medecin->tarif_heure * $medecin->heures_mois; @endphp
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white mr-4 font-bold shadow-md shadow-blue-100">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="font-bold text-gray-700">Dr. {{ strtoupper($medecin->nom) }} {{ $medecin->prenom }}</span>
                        </div>
                    </td>
                    <td class="p-5">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[11px] font-black uppercase">{{ $medecin->specialite->nom ?? 'Généraliste' }}</span>
                    </td>
                    <td class="p-5 text-sm text-gray-500 font-medium">
                        <span class="text-gray-700 flex items-center"><i class="fas fa-envelope mr-2 text-gray-300 text-[10px]"></i>{{ $medecin->email ?? 'Pas de compte' }}</span>
                        <span class="text-[11px] text-blue-500 font-bold mt-1"><i class="fas fa-phone-alt mr-2 text-gray-300 text-[10px]"></i>{{ $medecin->telephone }}</span>
                    </td>
                    <td class="p-5 text-center"><span class="bg-cyan-50 text-cyan-600 px-3 py-1 rounded-lg text-[11px] font-black">{{ number_format($medecin->tarif_heure, 0, ',', ' ') }} F</span></td>
                    <td class="p-5 text-center"><span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[11px] font-black">{{ $medecin->heures_mois }}h</span></td>
                    <td class="p-5 text-center"><span class="bg-green-50 text-green-700 px-3 py-1 rounded-lg text-xs font-black border border-green-100">{{ number_format($montantTotal, 0, ',', ' ') }} F</span></td>
                    <td class="p-5">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('medecins.edit', $medecin->id) }}" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('medecins.destroy', $medecin->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border-none cursor-pointer"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($medecins->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $medecins->links() }}
            </div>
        @endif

        <div id="emptyState" class="hidden py-20 text-center">
            <i class="fas fa-user-md text-5xl text-gray-200 mb-4"></i>
            <p class="text-gray-400 italic">Aucun médecin trouvé.</p>
        </div>
    </div>
</div>

<script>
let searchTimer;
const csrf = document.querySelector('meta[name="csrf-token"]').content;

function filtrerMedecins() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        const search = document.getElementById('searchMedecin').value;
        const specialite = document.getElementById('filterSpecialite').value;

        fetch(`/api/medecins/search?search=${encodeURIComponent(search)}&specialite_id=${specialite}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('medecinTableBody');
            const empty = document.getElementById('emptyState');
            const count = document.getElementById('countResult');

            count.textContent = data.length + ' résultat(s)';

            if (data.length === 0) {
                tbody.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');
            tbody.innerHTML = data.map(m => {
                const esc = s => {
                    const d = document.createElement('div');
                    d.textContent = s ?? '';
                    return d.innerHTML;
                };
                return `
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white mr-4 font-bold shadow-md shadow-blue-100">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <span class="font-bold text-gray-700">Dr. ${esc((m.nom || '').toUpperCase())} ${esc(m.prenom)}</span>
                        </div>
                    </td>
                    <td class="p-5"><span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[11px] font-black uppercase">${esc(m.specialite)}</span></td>
                    <td class="p-5 text-sm text-gray-500 font-medium">
                        <span class="text-gray-700 flex items-center"><i class="fas fa-envelope mr-2 text-gray-300 text-[10px]"></i>${esc(m.email)}</span>
                        <span class="text-[11px] text-blue-500 font-bold mt-1"><i class="fas fa-phone-alt mr-2 text-gray-300 text-[10px]"></i>${esc(m.telephone)}</span>
                    </td>
                    <td class="p-5 text-center"><span class="bg-cyan-50 text-cyan-600 px-3 py-1 rounded-lg text-[11px] font-black">${Number(m.tarif_heure).toLocaleString('fr-FR')} F</span></td>
                    <td class="p-5 text-center"><span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-[11px] font-black">${m.heures_mois}h</span></td>
                    <td class="p-5 text-center"><span class="bg-green-50 text-green-700 px-3 py-1 rounded-lg text-xs font-black border border-green-100">${Number(m.montant_total).toLocaleString('fr-FR')} F</span></td>
                    <td class="p-5">
                        <div class="flex justify-center gap-2">
                            <a href="${esc(m.edit_url)}" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm"><i class="fas fa-edit"></i></a>
                            <form action="${esc(m.delete_url)}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                <input type="hidden" name="_token" value="${csrf}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm border-none cursor-pointer"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            `}).join('');
        })
        .catch(() => {
            document.getElementById('countResult').textContent = 'Erreur de chargement';
        });
    }, 300);
}
</script>
@endsection
