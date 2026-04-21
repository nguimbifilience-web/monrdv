@extends('layouts.master')

@section('content')
<div class="p-8 max-w-6xl mx-auto">
    <div class="mb-6">
        @if(auth()->user()->isMedecin())
            <a href="{{ route('medecin.dossier-patient', $patient->id) }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
                <i class="fas fa-arrow-left"></i> Retour au dossier
            </a>
        @else
            <a href="{{ route('patients.show', $patient) }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
                <i class="fas fa-arrow-left"></i> Retour a la fiche patient
            </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
        @foreach($errors->all() as $error)
            <p class="text-xs font-bold">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- EN-TETE PATIENT --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-900 rounded-2xl flex items-center justify-center text-white text-xl font-black">
                {{ strtoupper(substr($patient->prenom, 0, 1)) }}{{ strtoupper(substr($patient->nom, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-blue-900 text-xl font-black uppercase">{{ $patient->nom }} {{ $patient->prenom }}</h1>
                <p class="text-gray-500 text-xs font-bold">Documents du patient</p>
            </div>
        </div>
    </div>

    {{-- ONGLETS --}}
    <div class="flex gap-2 mb-6 border-b border-gray-200">
        @if($peutVoirInfo)
        <button type="button" data-tab="info"
            class="tab-btn px-6 py-3 font-black text-xs uppercase tracking-widest border-b-2 border-cyan-500 text-cyan-600">
            <i class="fas fa-id-card mr-2"></i> Dossier Informations
            <span class="ml-2 bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-[10px]">{{ $documentsInfo->count() }}</span>
        </button>
        @endif
        @if($peutVoirMedical)
        <button type="button" data-tab="medical"
            class="tab-btn px-6 py-3 font-black text-xs uppercase tracking-widest border-b-2 border-transparent text-gray-400 hover:text-blue-900">
            <i class="fas fa-notes-medical mr-2"></i> Dossier Medical
            <span class="ml-2 bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-[10px]">{{ $documentsMedical->count() + $ordonnances->count() + $feuilles->count() }}</span>
        </button>
        @endif
    </div>

    {{-- ONGLET INFORMATIONS --}}
    @if($peutVoirInfo)
    <div id="tab-info" class="tab-content">
        @if($peutUploadInfo)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 mb-6">
            <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
                <i class="fas fa-cloud-upload-alt text-cyan-500 mr-2"></i> Ajouter aux informations
            </h3>
            <form action="{{ route('patients.documents.store', $patient) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
                @csrf
                <input type="hidden" name="categorie" value="informations">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nom</label>
                    <input type="text" name="nom" required placeholder="Ex: Carte d'identite"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 font-bold text-blue-900 text-xs focus:border-cyan-400 focus:ring-0">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Type</label>
                    <select name="type" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 font-bold text-blue-900 text-xs focus:border-cyan-400 focus:ring-0">
                        <option value="assurance">Assurance</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="min-w-[200px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fichier (PDF/JPG/PNG max 5Mo)</label>
                    <input type="file" name="fichier" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-2 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-600 hover:file:bg-cyan-100">
                </div>
                <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] hover:bg-blue-800">
                    <i class="fas fa-upload mr-1"></i> Envoyer
                </button>
            </form>
        </div>
        @endif

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden">
            @include('patients.partials.documents-table', ['documents' => $documentsInfo, 'patient' => $patient, 'categorie' => 'informations', 'peutSupprimer' => $peutUploadInfo])
        </div>
    </div>
    @endif

    {{-- ONGLET MEDICAL --}}
    @if($peutVoirMedical)
    <div id="tab-medical" class="tab-content hidden">
        @if($peutUploadMedical)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 mb-6">
            <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
                <i class="fas fa-cloud-upload-alt text-cyan-500 mr-2"></i> Ajouter au dossier medical
            </h3>
            <form action="{{ route('patients.documents.store', $patient) }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
                @csrf
                <input type="hidden" name="categorie" value="medical">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nom</label>
                    <input type="text" name="nom" required placeholder="Ex: Bilan sanguin 2026"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 font-bold text-blue-900 text-xs focus:border-cyan-400 focus:ring-0">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Type</label>
                    <select name="type" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 font-bold text-blue-900 text-xs focus:border-cyan-400 focus:ring-0">
                        <option value="ordonnance">Ordonnance</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="min-w-[200px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fichier (PDF/JPG/PNG max 5Mo)</label>
                    <input type="file" name="fichier" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-2 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-600 hover:file:bg-cyan-100">
                </div>
                <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] hover:bg-blue-800">
                    <i class="fas fa-upload mr-1"></i> Envoyer
                </button>
            </form>
        </div>
        @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 mb-6 rounded-xl">
            <p class="text-xs font-bold"><i class="fas fa-info-circle mr-1"></i> Lecture seule : seul le medecin peut ajouter ou supprimer dans le dossier medical.</p>
        </div>
        @endif

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden mb-6">
            @include('patients.partials.documents-table', ['documents' => $documentsMedical, 'patient' => $patient, 'categorie' => 'medical', 'peutSupprimer' => $peutUploadMedical])
        </div>

        {{-- ORDONNANCES --}}
        <div class="mt-8">
            <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
                <i class="fas fa-prescription text-blue-500 mr-2"></i> Ordonnances prescrites
            </h3>
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-50">
                        <tr class="text-[9px] font-black uppercase text-gray-300">
                            <th class="p-5">Date</th>
                            <th class="p-5">Medecin</th>
                            <th class="p-5">Lignes</th>
                            <th class="p-5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($ordonnances as $o)
                            <tr class="hover:bg-gray-50/30">
                                <td class="p-5 text-xs font-bold text-blue-900">{{ $o->date->format('d/m/Y') }}</td>
                                <td class="p-5 text-xs font-black text-blue-900 uppercase">Dr {{ $o->medecin->nom }} {{ $o->medecin->prenom }}</td>
                                <td class="p-5 text-xs text-gray-600">{{ $o->lignes->count() }} ligne(s)</td>
                                <td class="p-5 text-center">
                                    @if(auth()->user()->isMedecin())
                                    <a href="{{ route('medecin.ordonnances.show', $o) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-widest"><i class="fas fa-eye"></i> Voir</a>
                                    <a href="{{ route('medecin.ordonnances.print', $o) }}" target="_blank" class="ml-3 text-gray-500 hover:text-gray-700 text-xs font-bold uppercase tracking-widest"><i class="fas fa-print"></i> Imprimer</a>
                                    @else
                                    <span class="text-gray-300 text-xs font-bold uppercase">Lecture medecin uniquement</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-10 text-center text-xs text-gray-400 uppercase tracking-widest">Aucune ordonnance</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FEUILLES D'EXAMEN --}}
        <div class="mt-8">
            <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
                <i class="fas fa-vials text-purple-500 mr-2"></i> Feuilles d'examen
            </h3>
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-50">
                        <tr class="text-[9px] font-black uppercase text-gray-300">
                            <th class="p-5">Date</th>
                            <th class="p-5">Medecin</th>
                            <th class="p-5">Lignes</th>
                            <th class="p-5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($feuilles as $f)
                            <tr class="hover:bg-gray-50/30">
                                <td class="p-5 text-xs font-bold text-blue-900">{{ $f->date->format('d/m/Y') }}</td>
                                <td class="p-5 text-xs font-black text-blue-900 uppercase">Dr {{ $f->medecin->nom }} {{ $f->medecin->prenom }}</td>
                                <td class="p-5 text-xs text-gray-600">{{ $f->lignes->count() }} ligne(s)</td>
                                <td class="p-5 text-center">
                                    @if(auth()->user()->isMedecin())
                                    <a href="{{ route('medecin.examens.show', $f) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-widest"><i class="fas fa-eye"></i> Voir</a>
                                    <a href="{{ route('medecin.examens.print', $f) }}" target="_blank" class="ml-3 text-gray-500 hover:text-gray-700 text-xs font-bold uppercase tracking-widest"><i class="fas fa-print"></i> Imprimer</a>
                                    @else
                                    <span class="text-gray-300 text-xs font-bold uppercase">Lecture medecin uniquement</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-10 text-center text-xs text-gray-400 uppercase tracking-widest">Aucune feuille d'examen</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-cyan-500', 'text-cyan-600');
            b.classList.add('border-transparent', 'text-gray-400');
        });
        btn.classList.remove('border-transparent', 'text-gray-400');
        btn.classList.add('border-cyan-500', 'text-cyan-600');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
    });
});
</script>
@endpush
@endsection
