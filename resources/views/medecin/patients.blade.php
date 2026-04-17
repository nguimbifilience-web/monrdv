@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-blue-900 uppercase italic">Mes Patients</h1>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Patients ayant pris rendez-vous avec vous</p>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Patient</th>
                    <th class="p-5">Telephone</th>
                    <th class="p-5">Email</th>
                    <th class="p-5">Assurance</th>
                    <th class="p-5">Quartier</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-100">
                                {{ strtoupper(substr($patient->prenom, 0, 1)) }}{{ strtoupper(substr($patient->nom, 0, 1)) }}
                            </div>
                            <span class="font-black text-blue-900 text-xs uppercase">{{ $patient->nom }} {{ $patient->prenom }}</span>
                        </div>
                    </td>
                    <td class="p-5 text-xs font-bold text-gray-600">{{ $patient->telephone }}</td>
                    <td class="p-5 text-[10px] text-gray-400">{{ $patient->email ?? '—' }}</td>
                    <td class="p-5">
                        @if($patient->est_assure && $patient->assurance)
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black">
                                <i class="fas fa-shield-alt mr-1"></i>{{ $patient->assurance->nom }}
                            </span>
                        @else
                            <span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black">Non assure</span>
                        @endif
                    </td>
                    <td class="p-5 text-xs text-gray-500">{{ $patient->quartier ?? '—' }}</td>
                    <td class="p-5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('medecin.dossier-patient', $patient->id) }}"
                                class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors">
                                <i class="fas fa-folder-open mr-1"></i> Dossier
                            </a>
                            <button type="button"
                                onclick="ouvrirModalProchainRdv({{ $patient->id }}, '{{ addslashes($patient->nom . ' ' . $patient->prenom) }}')"
                                class="bg-cyan-50 text-cyan-600 hover:bg-cyan-100 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors">
                                <i class="fas fa-calendar-plus mr-1"></i> Prochain RDV
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-20 text-center">
                        <i class="fas fa-user-slash text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun patient</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($patients->hasPages())
        <div class="p-4 bg-gray-50/50 border-t border-gray-50">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
{{-- MODAL PROGRAMMER PROCHAIN RDV --}}
<div id="modalProchainRdv" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="fermerModalProchainRdv()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-blue-900 p-6">
            <h3 class="text-white text-lg font-black uppercase">Programmer prochain RDV</h3>
            <p class="text-blue-300 text-xs font-bold mt-1" id="modalPatientName"></p>
        </div>
        <form action="{{ route('medecin.prochain-rdv') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="patient_id" id="modalPatientId">

            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date du prochain RDV</label>
                <input type="date" name="date_rv" required min="{{ now()->addDay()->format('Y-m-d') }}"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0">
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Motif</label>
                <select id="modalMotifSelect" onchange="onModalMotifSelect()"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 mb-2">
                    <option value="">— Choisir un motif —</option>
                    @foreach($motifs as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                    <option value="__autre__">Autre (saisie libre)</option>
                </select>
                <textarea name="motif" id="modalMotifText" rows="2" required
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-gray-600 text-sm focus:border-cyan-400 focus:ring-0 resize-none"
                    placeholder="Motif de la prochaine consultation..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-900 text-white font-black py-3 rounded-xl hover:bg-blue-800 uppercase text-xs tracking-widest">
                    <i class="fas fa-calendar-check mr-1"></i> Programmer
                </button>
                <button type="button" onclick="fermerModalProchainRdv()" class="px-6 py-3 text-gray-400 font-bold hover:text-red-500 uppercase text-[10px]">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function ouvrirModalProchainRdv(patientId, patientName) {
    document.getElementById('modalPatientId').value = patientId;
    document.getElementById('modalPatientName').textContent = 'Patient : ' + patientName;
    document.getElementById('modalMotifText').value = '';
    document.getElementById('modalMotifSelect').value = '';
    document.getElementById('modalProchainRdv').classList.remove('hidden');
}

function fermerModalProchainRdv() {
    document.getElementById('modalProchainRdv').classList.add('hidden');
}

function onModalMotifSelect() {
    const select = document.getElementById('modalMotifSelect');
    const textarea = document.getElementById('modalMotifText');
    if (select.value && select.value !== '__autre__') {
        textarea.value = select.value;
    } else if (select.value === '__autre__') {
        textarea.value = '';
        textarea.focus();
    }
}
</script>
@endpush
@endsection
