@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Patients</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Gestion complète des dossiers patients</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('exports.patients') }}" class="bg-white border-2 border-gray-200 text-gray-700 px-6 py-4 rounded-2xl font-black text-xs uppercase hover:border-blue-400 hover:text-blue-600 transition-all">
                <i class="fas fa-file-csv mr-2"></i> Exporter CSV
            </a>
            <a href="{{ route('patients.create') }}" class="bg-cyan-400 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-lg hover:scale-105 transition-all">
                + Nouveau Patient
            </a>
        </div>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500"><i class="fas fa-users text-xl"></i></div>
            <div>
                <p class="text-2xl font-black text-blue-900">{{ $totalPatients }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase">Total patients</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500"><i class="fas fa-shield-alt text-xl"></i></div>
            <div>
                <p class="text-2xl font-black text-green-600">{{ $patientsAssures }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase">Patients assurés</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-400"><i class="fas fa-user-times text-xl"></i></div>
            <div>
                <p class="text-2xl font-black text-red-500">{{ $totalPatients - $patientsAssures }}</p>
                <p class="text-[10px] font-black text-gray-400 uppercase">Non assurés</p>
            </div>
        </div>
    </div>

    {{-- BARRE DE FILTRES AJAX --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                <input type="text" id="searchPatient" placeholder="Rechercher par nom, prénom ou téléphone..."
                    class="w-full bg-gray-50 border-none rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 placeholder-gray-400" oninput="filtrerPatients()">
            </div>
            <select id="filterMedecin" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[180px]" onchange="filtrerPatients()">
                <option value="">Tous les médecins</option>
                @foreach($medecins as $m)
                    <option value="{{ $m->id }}">Dr. {{ $m->nom }}</option>
                @endforeach
            </select>
            <select id="filterAssurance" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[160px]" onchange="filtrerPatients()">
                <option value="">Toutes assurances</option>
                @foreach($assurances as $a)
                    <option value="{{ $a->id }}">{{ $a->nom }}</option>
                @endforeach
            </select>
            <select id="filterAssure" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[130px]" onchange="filtrerPatients()">
                <option value="">Statut</option>
                <option value="1">Assuré</option>
                <option value="0">Non assuré</option>
            </select>
            <span id="countPatients" class="text-[10px] font-black text-gray-400 uppercase whitespace-nowrap"></span>
        </div>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-6">Patient</th>
                    <th class="p-6">Contact</th>
                    <th class="p-6">Assurance</th>
                    <th class="p-6">Médecin</th>
                    <th class="p-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="patientTableBody" class="divide-y divide-gray-50">
                @foreach($patients as $patient)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-100">
                                {{ strtoupper(substr($patient->prenom, 0, 1)) }}{{ strtoupper(substr($patient->nom, 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-black text-blue-900 text-xs uppercase">{{ $patient->nom }} {{ $patient->prenom }}</span>
                                <p class="text-[10px] text-gray-400">{{ $patient->quartier ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-6">
                        <div class="text-[10px] font-black text-blue-900">{{ $patient->telephone }}</div>
                        <div class="text-[9px] text-gray-400 lowercase">{{ $patient->email }}</div>
                    </td>
                    <td class="p-6">
                        @if($patient->est_assure && $patient->assurance)
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black"><i class="fas fa-shield-alt mr-1"></i>{{ $patient->assurance->nom }}</span>
                        @else
                            <span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black">Non assuré</span>
                        @endif
                    </td>
                    <td class="p-6">
                        @if($patient->medecin)
                            <span class="text-xs font-bold text-gray-600">Dr. {{ $patient->medecin->nom }}</span>
                        @else
                            <span class="text-xs text-gray-300 italic">Aucun</span>
                        @endif
                    </td>
                    <td class="p-6">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('patients.show', $patient) }}" class="w-9 h-9 flex items-center justify-center bg-cyan-50 text-cyan-500 rounded-xl hover:bg-cyan-500 hover:text-white transition-all"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('patients.edit', $patient) }}" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div id="emptyPatients" class="hidden p-20 text-center">
            <i class="fas fa-user-slash text-4xl text-gray-200 mb-3"></i>
            <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun patient trouvé</p>
        </div>

        @if($patients->hasPages())
        <div id="paginationPatients" class="p-4 bg-gray-50/50 border-t border-gray-50">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>

@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif

<script>
let timerPatient;
const csrf = document.querySelector('meta[name="csrf-token"]').content;

function filtrerPatients() {
    clearTimeout(timerPatient);
    timerPatient = setTimeout(() => {
        const params = new URLSearchParams({
            search: document.getElementById('searchPatient').value,
            medecin_id: document.getElementById('filterMedecin').value,
            assurance_id: document.getElementById('filterAssurance').value,
            est_assure: document.getElementById('filterAssure').value,
        });

        fetch(`/ajax/patients/search?${params}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('patientTableBody');
            const empty = document.getElementById('emptyPatients');
            const pagination = document.getElementById('paginationPatients');
            const count = document.getElementById('countPatients');

            count.textContent = data.length + ' résultat(s)';
            if (pagination) pagination.classList.add('hidden');

            if (data.length === 0) {
                tbody.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');
            tbody.innerHTML = data.map(p => `
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-100">
                                ${(p.prenom||'').charAt(0).toUpperCase()}${(p.nom||'').charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <span class="font-black text-blue-900 text-xs uppercase">${p.nom} ${p.prenom}</span>
                                <p class="text-[10px] text-gray-400">${p.quartier || ''}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-6">
                        <div class="text-[10px] font-black text-blue-900">${p.telephone || ''}</div>
                        <div class="text-[9px] text-gray-400 lowercase">${p.email || ''}</div>
                    </td>
                    <td class="p-6">
                        ${p.est_assure && p.assurance_nom
                            ? `<span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black"><i class="fas fa-shield-alt mr-1"></i>${p.assurance_nom}</span>`
                            : `<span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black">Non assuré</span>`}
                    </td>
                    <td class="p-6">
                        ${p.medecin_nom
                            ? `<span class="text-xs font-bold text-gray-600">${p.medecin_nom}</span>`
                            : `<span class="text-xs text-gray-300 italic">Aucun</span>`}
                    </td>
                    <td class="p-6">
                        <div class="flex justify-center gap-2">
                            <a href="${p.show_url}" class="w-9 h-9 flex items-center justify-center bg-cyan-50 text-cyan-500 rounded-xl hover:bg-cyan-500 hover:text-white transition-all"><i class="fas fa-eye"></i></a>
                            <a href="${p.edit_url}" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-edit"></i></a>
                            <form action="${p.delete_url}" method="POST" onsubmit="return confirm('Supprimer ?')">
                                <input type="hidden" name="_token" value="${csrf}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            `).join('');
        })
        .catch(() => {
            document.getElementById('countPatients').textContent = 'Erreur de chargement';
        });
    }, 300);
}
</script>
@endsection
