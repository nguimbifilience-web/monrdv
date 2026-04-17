@extends('layouts.master')

@section('content')
<div class="p-8 max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('medecin.patients') }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour a mes patients
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
    @endif

    {{-- EN-TETE PATIENT --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden mb-6">
        <div class="bg-blue-900 p-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-white text-xl font-black">
                    {{ strtoupper(substr($patient->prenom, 0, 1)) }}{{ strtoupper(substr($patient->nom, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-white text-xl font-black uppercase">{{ $patient->nom }} {{ $patient->prenom }}</h1>
                    <p class="text-blue-300 text-xs font-bold">{{ $patient->telephone }} &bull; {{ $patient->email ?? 'Pas d\'email' }}</p>
                </div>
            </div>
            <button type="button"
                onclick="ouvrirModalProchainRdv({{ $patient->id }}, '{{ addslashes($patient->nom . ' ' . $patient->prenom) }}')"
                class="bg-cyan-400 text-white hover:bg-cyan-500 px-5 py-3 rounded-xl font-black uppercase text-xs transition-colors">
                <i class="fas fa-calendar-plus mr-2"></i> Programmer prochain RDV
            </button>
        </div>
        <div class="p-6 grid grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Quartier</p>
                <p class="text-sm font-bold text-blue-900">{{ $patient->quartier ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Assurance</p>
                @if($patient->est_assure && $patient->assurance)
                    <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black">
                        <i class="fas fa-shield-alt mr-1"></i>{{ $patient->assurance->nom }}
                    </span>
                @else
                    <span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black">Non assure</span>
                @endif
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Total RDV</p>
                <p class="text-sm font-bold text-blue-900">{{ $rendezvous->count() }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Dernier RDV</p>
                <p class="text-sm font-bold text-blue-900">
                    {{ $rendezvous->first() ? \Carbon\Carbon::parse($rendezvous->first()->date_rv)->format('d/m/Y') : '—' }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        {{-- NOTES MEDICALES --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden">
            <div class="p-5 border-b border-gray-50">
                <h2 class="text-sm font-black text-blue-900 uppercase">
                    <i class="fas fa-notes-medical mr-2 text-cyan-500"></i> Notes medicales
                </h2>
            </div>
            <form action="{{ route('medecin.sauvegarder-notes', $patient->id) }}" method="POST" class="p-5">
                @csrf
                <textarea name="notes_medicales" rows="8"
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-4 text-sm text-gray-700 focus:border-cyan-400 focus:ring-0 resize-none mb-3"
                    placeholder="Antecedents, allergies, traitements en cours...">{{ $patient->notes_medicales }}</textarea>

                <div class="mb-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observations</label>
                    <textarea name="observations" rows="5"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-4 text-sm text-gray-700 focus:border-cyan-400 focus:ring-0 resize-none"
                        placeholder="Observations du medecin...">{{ $patient->observations }}</textarea>
                </div>

                <button type="submit" class="bg-blue-900 text-white font-black px-6 py-3 rounded-xl hover:bg-blue-800 uppercase text-[10px] tracking-widest">
                    <i class="fas fa-save mr-1"></i> Sauvegarder
                </button>
            </form>
        </div>

        {{-- HISTORIQUE RDV --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden">
            <div class="p-5 border-b border-gray-50">
                <h2 class="text-sm font-black text-blue-900 uppercase">
                    <i class="fas fa-history mr-2 text-cyan-500"></i> Historique des rendez-vous
                </h2>
            </div>
            <div class="divide-y divide-gray-50 max-h-[500px] overflow-y-auto">
                @forelse($rendezvous as $rdv)
                <div class="p-4 flex items-center justify-between hover:bg-gray-50/30 transition-colors">
                    <div>
                        <p class="font-black text-blue-900 text-xs">
                            {{ \Carbon\Carbon::parse($rdv->date_rv)->format('d/m/Y') }}
                            @if($rdv->heure_rv)
                                <span class="text-gray-400 ml-1">{{ \Carbon\Carbon::parse($rdv->heure_rv)->format('H:i') }}</span>
                            @endif
                        </p>
                        @if($rdv->motif)
                            <p class="text-[10px] text-gray-500 mt-1">{{ $rdv->motif }}</p>
                        @endif
                    </div>
                    <div>
                        @if($rdv->statut === 'en_attente')
                            <span class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">En attente</span>
                        @elseif($rdv->statut === 'confirme')
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Confirme</span>
                        @elseif($rdv->statut === 'termine')
                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Termine</span>
                        @else
                            <span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Annule</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <p class="text-gray-400 italic text-xs font-bold">Aucun rendez-vous</p>
                </div>
                @endforelse
            </div>
        </div>
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
