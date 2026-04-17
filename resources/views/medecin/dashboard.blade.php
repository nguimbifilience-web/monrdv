@extends('layouts.master')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-blue-900 uppercase italic">Bonjour, Dr. {{ $medecin->nom }}</h1>
    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">{{ $medecin->specialite->nom ?? 'Generaliste' }} — {{ now()->translatedFormat('l d F Y') }}</p>
</div>

{{-- STATISTIQUES --}}
<div class="grid grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
            <i class="fas fa-calendar-day text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-orange-500">{{ $rdvAujourdhui }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">RDV aujourd'hui</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
            <i class="fas fa-calendar text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-blue-900">{{ $totalRdv }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">Total RDV</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500">
            <i class="fas fa-users text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-black text-green-600">{{ $totalPatients }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase">Patients</p>
        </div>
    </div>
</div>

{{-- RDV DU JOUR --}}
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
    <div class="p-6 border-b border-gray-50">
        <h2 class="text-sm font-black text-blue-900 uppercase">Rendez-vous du jour</h2>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($rdvsAujourdhui as $rdv)
        <div class="p-6 flex items-center justify-between hover:bg-gray-50/30 transition-colors">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-100">
                    {{ strtoupper(substr($rdv->patient->prenom ?? '', 0, 1)) }}{{ strtoupper(substr($rdv->patient->nom ?? '', 0, 1)) }}
                </div>
                <div>
                    <p class="font-black text-blue-900 text-sm uppercase">{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</p>
                    <p class="text-[10px] text-gray-400 font-bold">{{ $rdv->patient->telephone }}</p>
                    @if($rdv->motif)
                        <p class="text-[10px] text-gray-500 mt-1">{{ $rdv->motif }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="bg-blue-50 text-blue-700 px-4 py-2 rounded-xl text-xs font-black">
                    {{ \Carbon\Carbon::parse($rdv->heure_rv)->format('H:i') }}
                </span>
                @if($rdv->statut === 'en_attente')
                    <span class="bg-orange-50 text-orange-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">En attente</span>
                @else
                    <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Termine</span>
                @endif
                <button type="button"
                    onclick="ouvrirModalProchainRdv({{ $rdv->patient->id }}, '{{ addslashes($rdv->patient->nom . ' ' . $rdv->patient->prenom) }}')"
                    class="bg-cyan-50 text-cyan-600 hover:bg-cyan-100 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase transition-colors"
                    title="Programmer prochain RDV">
                    <i class="fas fa-calendar-plus"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-calendar-check text-4xl text-gray-200 mb-3"></i>
            <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun rendez-vous aujourd'hui</p>
        </div>
        @endforelse
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
