@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Rendez-vous</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Planification et suivi des consultations</p>
        </div>
        <div class="flex gap-3">
            <button onclick="toggleModal('modalConsultation')" class="bg-green-500 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-lg hover:scale-105 transition-all">
                <i class="fas fa-stethoscope mr-2"></i> Consultation
            </button>
            <a href="{{ route('rendezvous.create') }}" class="bg-cyan-400 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-lg hover:scale-105 transition-all">
                + Nouveau RDV
            </a>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 mb-6">
        <form action="{{ route('rendezvous.index') }}" method="GET" class="flex items-center gap-4">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un patient..."
                    class="w-full bg-gray-50 border-none rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 placeholder-gray-400">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900">
            <select name="medecin_id" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[170px]">
                <option value="">Tous les médecins</option>
                @foreach($medecins as $m)
                    <option value="{{ $m->id }}" {{ request('medecin_id') == $m->id ? 'selected' : '' }}>Dr. {{ $m->nom }}</option>
                @endforeach
            </select>
            <select name="statut" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[140px]">
                <option value="">Tous statuts</option>
                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="confirme" {{ request('statut') == 'confirme' ? 'selected' : '' }}>Confirmé</option>
                <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
            </select>
            <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px]">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
            @if(request()->anyFilled(['search','date','medecin_id','statut']))
                <a href="{{ route('rendezvous.index') }}" class="text-gray-400 hover:text-red-400 text-xs font-bold"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    {{-- MESSAGES --}}
    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-2xl flex items-center">
        <i class="fas fa-exclamation-triangle mr-3"></i>
        <span class="font-bold text-sm">{{ session('error') }}</span>
    </div>
    @endif

    {{-- TABLEAU --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Date / Heure</th>
                    <th class="p-5">Patient</th>
                    <th class="p-5">Médecin / Spécialité</th>
                    <th class="p-5">Motif</th>
                    <th class="p-5 text-center">Statut</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rendezvous as $rdv)
                <tr class="hover:bg-gray-50/30 transition-colors {{ $rdv->statut === 'annule' ? 'opacity-50' : '' }}">
                    <td class="p-5">
                        <div class="font-black text-blue-900 text-xs">{{ \Carbon\Carbon::parse($rdv->date_rv)->format('d/m/Y') }}</div>
                        <div class="text-[10px] text-cyan-500 font-bold"><i class="fas fa-clock mr-1"></i>{{ $rdv->heure_rv }}</div>
                    </td>
                    <td class="p-5">
                        <span class="font-black text-blue-900 text-xs uppercase">{{ $rdv->patient->nom ?? '' }} {{ $rdv->patient->prenom ?? '' }}</span>
                        @if($rdv->patient && $rdv->patient->est_assure && $rdv->patient->assurance)
                            <div class="text-[9px] text-green-500 font-bold mt-1">
                                <i class="fas fa-shield-alt mr-1"></i>{{ $rdv->patient->assurance->nom }} ({{ $rdv->patient->assurance->taux_couverture }}%)
                            </div>
                        @endif
                    </td>
                    <td class="p-5">
                        <div class="text-xs font-bold text-gray-700">Dr. {{ $rdv->medecin->nom ?? '' }}</div>
                        <span class="bg-indigo-50 text-indigo-500 px-2 py-0.5 rounded text-[9px] font-black uppercase">
                            {{ $rdv->medecin->specialite->nom ?? 'Généraliste' }}
                        </span>
                    </td>
                    <td class="p-5 text-xs text-gray-500 max-w-[150px] truncate">{{ $rdv->motif ?? '-' }}</td>
                    <td class="p-5 text-center">
                        @if($rdv->statut === 'confirme')
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black">Confirmé</span>
                        @elseif($rdv->statut === 'annule')
                            <span class="bg-red-50 text-red-500 px-3 py-1 rounded-lg text-[10px] font-black">Annulé</span>
                        @elseif($rdv->statut === 'termine')
                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black">Terminé</span>
                        @else
                            <span class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-[10px] font-black">En attente</span>
                        @endif
                    </td>
                    <td class="p-5">
                        <div class="flex justify-center gap-1">
                            @if($rdv->statut !== 'annule' && $rdv->statut !== 'termine')
                                <form action="{{ route('rendezvous.annuler', $rdv->id) }}" method="POST" onsubmit="return confirm('Annuler ce rendez-vous ?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-orange-50 text-orange-500 rounded-lg hover:bg-orange-500 hover:text-white transition-all" title="Annuler le RDV">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>
                                </form>
                                <a href="{{ route('rendezvous.edit', $rdv) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all" title="Modifier">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            @endif

                            @if($rdv->statut === 'termine' && $rdv->consultation)
                                <a href="{{ route('consultations.ticket', $rdv->consultation->id) }}" class="w-8 h-8 flex items-center justify-center bg-cyan-50 text-cyan-500 rounded-lg hover:bg-cyan-500 hover:text-white transition-all" title="Voir ticket">
                                    <i class="fas fa-receipt text-xs"></i>
                                </a>
                            @endif

                            <form action="{{ route('rendezvous.destroy', $rdv) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all" title="Supprimer">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-20 text-center">
                        <i class="fas fa-calendar-times text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun rendez-vous trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($rendezvous->hasPages())
        <div class="p-4 bg-gray-50/50 border-t border-gray-50">
            {{ $rendezvous->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ============================================= --}}
{{-- MODAL CONSULTATION --}}
{{-- ============================================= --}}
<div id="modalConsultation" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-xl p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
        <h2 class="text-xl font-black text-blue-900 uppercase italic mb-6">
            <i class="fas fa-stethoscope text-green-500 mr-2"></i> Consultation
        </h2>

        <form action="{{ route('consultations.store') }}" method="POST" id="consultForm">
            @csrf

            {{-- 1. Sélection Patient --}}
            <div class="mb-4">
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Patient</label>
                <select name="patient_id" id="select_patient" onchange="chargerInfoPatient(this.value)"
                    class="w-full bg-gray-50 border-none rounded-xl p-4 font-bold text-blue-900 text-sm" required>
                    <option value="">-- Sélectionner un patient --</option>
                    @foreach(\App\Models\Patient::orderBy('nom')->get() as $p)
                        <option value="{{ $p->id }}">{{ strtoupper($p->nom) }} {{ $p->prenom }} — {{ $p->telephone }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Infos récupérées automatiquement --}}
            <div id="bloc_info" class="hidden space-y-3 mb-6">
                {{-- Patient + Médecin + Spécialité --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <span class="text-[9px] font-black text-gray-400 uppercase block">Patient</span>
                        <span id="info_nom" class="font-black text-blue-900 text-sm"></span>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <span class="text-[9px] font-black text-gray-400 uppercase block">Téléphone</span>
                        <span id="info_tel" class="font-bold text-gray-700 text-sm"></span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100">
                        <span class="text-[9px] font-black text-indigo-400 uppercase block">Médecin (du RDV)</span>
                        <span id="info_medecin" class="font-black text-indigo-700 text-sm"></span>
                    </div>
                    <div class="bg-indigo-50 rounded-xl p-3 border border-indigo-100">
                        <span class="text-[9px] font-black text-indigo-400 uppercase block">Spécialité</span>
                        <span id="info_specialite" class="font-black text-indigo-700 text-sm"></span>
                    </div>
                </div>
                <input type="hidden" name="medecin_id" id="hidden_medecin_id">

                {{-- 3. Assuré ou non --}}
                <div class="bg-gray-50 rounded-xl p-4">
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-3">Le patient est-il assuré ?</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="est_assure" value="1" id="radio_assure_oui" onchange="toggleAssurance()" class="text-green-500 focus:ring-green-400">
                            <span class="text-xs font-black text-gray-600">Oui, assuré</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="est_assure" value="0" id="radio_assure_non" onchange="toggleAssurance()" class="text-red-500 focus:ring-red-400" checked>
                            <span class="text-xs font-black text-gray-600">Non assuré</span>
                        </label>
                    </div>
                </div>

                {{-- Bloc assuré : affiche assurance, taux, calcul --}}
                <div id="bloc_assure" class="hidden bg-green-50 rounded-xl p-4 border border-green-100 space-y-2">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-[9px] font-black text-green-600 uppercase block">Assurance</span>
                            <span id="info_assurance_nom" class="font-black text-green-700 text-sm"></span>
                        </div>
                        <span id="info_taux_badge" class="bg-green-200 text-green-800 px-3 py-1 rounded-lg text-xs font-black"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-green-200">
                        <div class="text-center">
                            <span class="text-[8px] font-black text-gray-400 uppercase block">Tarif spécialité</span>
                            <span id="info_tarif_spec" class="font-black text-blue-900 text-sm"></span>
                        </div>
                        <div class="text-center">
                            <span class="text-[8px] font-black text-green-600 uppercase block">Part assurance</span>
                            <span id="info_part_assurance" class="font-black text-green-600 text-sm"></span>
                        </div>
                        <div class="text-center">
                            <span class="text-[8px] font-black text-blue-600 uppercase block">Part patient</span>
                            <span id="info_part_patient" class="font-black text-blue-700 text-sm"></span>
                        </div>
                    </div>
                </div>

                {{-- Bloc non assuré --}}
                <div id="bloc_non_assure" class="bg-red-50 rounded-xl p-4 border border-red-100">
                    <div class="flex justify-between items-center">
                        <span class="text-red-500 font-black text-xs uppercase"><i class="fas fa-times-circle mr-1"></i> Non assuré</span>
                        <span id="info_tarif_net" class="font-black text-red-600 text-lg"></span>
                    </div>
                </div>

                {{-- 4. Montants --}}
                <input type="hidden" name="tarif_specialite" id="hidden_tarif_specialite">
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 space-y-3">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-blue-700 mb-2">Montant à payer par le patient (FCFA)</label>
                        <input type="number" name="montant_total" id="input_montant" min="0" step="500" readonly
                            class="w-full bg-white border-2 border-blue-200 rounded-xl p-4 font-black text-blue-900 text-xl">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Montant donné par le patient (FCFA)</label>
                        <input type="number" name="montant_donne" id="input_donne" min="0" step="500" placeholder="Ex: 10000"
                            class="w-full bg-white border-none rounded-xl p-4 font-black text-blue-900 text-lg" required oninput="calculerRendu()">
                    </div>
                    <div id="bloc_rendu" class="hidden flex justify-between items-center bg-orange-50 rounded-xl p-4 border border-orange-100">
                        <span class="font-black text-orange-600 uppercase text-xs">Montant à rendre</span>
                        <span id="calc_rendu" class="font-black text-orange-600 text-xl"></span>
                    </div>
                </div>
            </div>

            {{-- 5. Boutons --}}
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="toggleModal('modalConsultation')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" name="action" value="save" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-blue-700 transition-all">
                    <i class="fas fa-save mr-1"></i> Enregistrer
                </button>
                <button type="submit" name="action" value="print" class="bg-green-500 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-green-600 transition-all">
                    <i class="fas fa-print mr-1"></i> Enregistrer & Imprimer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentTaux = 0;
    let currentTarif = 0;

    function toggleModal(id) { document.getElementById(id).classList.toggle('hidden'); }

    function chargerInfoPatient(patientId) {
        if (!patientId) {
            document.getElementById('bloc_info').classList.add('hidden');
            return;
        }

        fetch('/api/patients/' + patientId + '/info')
            .then(r => r.json())
            .then(data => {
                // Infos patient
                document.getElementById('info_nom').textContent = data.nom + ' ' + data.prenom;
                document.getElementById('info_tel').textContent = data.telephone;

                // Médecin du RDV
                if (data.medecin_id) {
                    document.getElementById('info_medecin').textContent = 'Dr. ' + data.medecin_nom + ' ' + (data.medecin_prenom || '');
                    document.getElementById('info_specialite').textContent = data.specialite_nom || 'Généraliste';
                    document.getElementById('hidden_medecin_id').value = data.medecin_id;
                    currentTarif = parseFloat(data.tarif_consultation) || 0;
                } else {
                    document.getElementById('info_medecin').textContent = 'Aucun RDV trouvé';
                    document.getElementById('info_specialite').textContent = '—';
                    document.getElementById('hidden_medecin_id').value = '';
                    currentTarif = 0;
                }

                // Assurance
                currentTaux = parseFloat(data.taux_couverture) || 0;

                if (data.est_assure && data.assurance_nom) {
                    document.getElementById('radio_assure_oui').checked = true;
                    document.getElementById('info_assurance_nom').textContent = data.assurance_nom;
                    document.getElementById('info_taux_badge').textContent = currentTaux + '%';
                } else {
                    document.getElementById('radio_assure_non').checked = true;
                }

                // Afficher tarifs
                document.getElementById('info_tarif_spec').textContent = currentTarif.toLocaleString('fr-FR') + ' F';
                document.getElementById('info_tarif_net').textContent = currentTarif.toLocaleString('fr-FR') + ' FCFA';

                document.getElementById('hidden_tarif_specialite').value = currentTarif;
                document.getElementById('bloc_info').classList.remove('hidden');
                toggleAssurance();
            });
    }

    function toggleAssurance() {
        const estAssure = document.getElementById('radio_assure_oui').checked;

        if (estAssure && currentTaux > 0) {
            // Assuré : le patient paie seulement sa part
            const partAssurance = Math.round(currentTarif * currentTaux / 100);
            const partPatient = currentTarif - partAssurance;

            document.getElementById('info_part_assurance').textContent = partAssurance.toLocaleString('fr-FR') + ' F';
            document.getElementById('info_part_patient').textContent = partPatient.toLocaleString('fr-FR') + ' F';

            document.getElementById('bloc_assure').classList.remove('hidden');
            document.getElementById('bloc_non_assure').classList.add('hidden');

            // Montant à payer = part patient uniquement
            document.getElementById('input_montant').value = partPatient;
        } else {
            // Non assuré : le patient paie le tarif complet de la spécialité
            document.getElementById('bloc_assure').classList.add('hidden');
            document.getElementById('bloc_non_assure').classList.remove('hidden');

            // Montant à payer = tarif consultation complet
            document.getElementById('input_montant').value = currentTarif;
        }

        document.getElementById('input_donne').value = '';
        document.getElementById('bloc_rendu').classList.add('hidden');
    }

    function calculerRendu() {
        const montantAPayer = parseFloat(document.getElementById('input_montant').value) || 0;
        const donne = parseFloat(document.getElementById('input_donne').value) || 0;

        const rendu = Math.max(0, donne - montantAPayer);

        if (donne > 0) {
            document.getElementById('calc_rendu').textContent = rendu.toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('bloc_rendu').classList.remove('hidden');
        } else {
            document.getElementById('bloc_rendu').classList.add('hidden');
        }
    }
</script>

{{-- FLASH --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
