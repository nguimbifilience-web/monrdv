@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Mes Documents</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Dossier informations et dossier medical</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
        @foreach($errors->all() as $error)
            <p class="text-xs font-bold">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- FORMULAIRE UPLOAD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-6 mb-6">
        <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
            <i class="fas fa-cloud-upload-alt text-cyan-500 mr-2"></i> Televerser un document
        </h3>

        <form action="{{ route('patient.documents.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dossier de destination</label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="categorie" value="informations" required class="peer sr-only" checked>
                        <div class="p-4 border-2 border-gray-100 rounded-xl peer-checked:border-cyan-400 peer-checked:bg-cyan-50 text-center transition-all">
                            <i class="fas fa-id-card text-cyan-500 text-xl mb-2"></i>
                            <p class="font-black text-blue-900 text-xs uppercase">Dossier Informations</p>
                            <p class="text-[10px] text-gray-400 mt-1">Carte identite, assurance...</p>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="categorie" value="medical" required class="peer sr-only">
                        <div class="p-4 border-2 border-gray-100 rounded-xl peer-checked:border-cyan-400 peer-checked:bg-cyan-50 text-center transition-all">
                            <i class="fas fa-notes-medical text-purple-500 text-xl mb-2"></i>
                            <p class="font-black text-blue-900 text-xs uppercase">Dossier Medical</p>
                            <p class="text-[10px] text-gray-400 mt-1">Papiers hopital, examens...</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nom du document</label>
                    <input type="text" name="nom" required placeholder="Ex: Carte CNAMGS 2026"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 font-bold text-blue-900 text-xs focus:border-cyan-400 focus:ring-0">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Type</label>
                    <select name="type" required
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 font-bold text-blue-900 text-xs focus:border-cyan-400 focus:ring-0">
                        <option value="assurance">Assurance</option>
                        <option value="ordonnance">Ordonnance</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="min-w-[200px]">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fichier (PDF, JPG, PNG - max 5Mo)</label>
                    <input type="file" name="fichier" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-2 text-xs text-gray-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-cyan-50 file:text-cyan-600 hover:file:bg-cyan-100 transition-all">
                </div>
                <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px] hover:bg-blue-800 transition-all">
                    <i class="fas fa-upload mr-1"></i> Envoyer
                </button>
            </div>
        </form>
    </div>

    {{-- DOSSIER INFORMATIONS --}}
    <div class="mt-8">
        <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
            <i class="fas fa-id-card text-cyan-500 mr-2"></i> Dossier Informations ({{ $documentsInfo->count() }})
        </h3>
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
            @include('patient.partials.documents-table', ['documents' => $documentsInfo])
        </div>
    </div>

    {{-- DOSSIER MEDICAL --}}
    <div class="mt-8">
        <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
            <i class="fas fa-notes-medical text-purple-500 mr-2"></i> Dossier Medical ({{ $documentsMedical->count() }})
        </h3>
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
            @include('patient.partials.documents-table', ['documents' => $documentsMedical])
        </div>
    </div>

    {{-- ORDONNANCES PRESCRITES --}}
    <div class="mt-8">
        <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
            <i class="fas fa-prescription text-blue-500 mr-2"></i> Ordonnances prescrites par mon medecin
        </h3>
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 border-b border-gray-50">
                    <tr class="text-[9px] font-black uppercase text-gray-300">
                        <th class="p-5">Date</th>
                        <th class="p-5">Medecin</th>
                        <th class="p-5">Medicaments</th>
                        <th class="p-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ordonnances as $o)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-5 text-xs font-bold text-blue-900">{{ $o->date->format('d/m/Y') }}</td>
                            <td class="p-5 text-xs font-black text-blue-900 uppercase">Dr {{ $o->medecin->nom }} {{ $o->medecin->prenom }}</td>
                            <td class="p-5 text-xs text-gray-600">{{ $o->lignes->count() }} ligne(s)</td>
                            <td class="p-5 text-center">
                                <a href="{{ route('patient.ordonnances.show', $o) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-widest"><i class="fas fa-eye"></i> Voir</a>
                                <a href="{{ route('patient.ordonnances.print', $o) }}" target="_blank" class="ml-3 text-gray-500 hover:text-gray-700 text-xs font-bold uppercase tracking-widest"><i class="fas fa-print"></i> Imprimer</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-10 text-center text-xs text-gray-400 uppercase tracking-widest">Aucune ordonnance</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FEUILLES D'EXAMEN PRESCRITES --}}
    <div class="mt-8">
        <h3 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">
            <i class="fas fa-vials text-purple-500 mr-2"></i> Feuilles d'examen prescrites
        </h3>
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 border-b border-gray-50">
                    <tr class="text-[9px] font-black uppercase text-gray-300">
                        <th class="p-5">Date</th>
                        <th class="p-5">Medecin</th>
                        <th class="p-5">Examens</th>
                        <th class="p-5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($feuilles as $f)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-5 text-xs font-bold text-blue-900">{{ $f->date->format('d/m/Y') }}</td>
                            <td class="p-5 text-xs font-black text-blue-900 uppercase">Dr {{ $f->medecin->nom }} {{ $f->medecin->prenom }}</td>
                            <td class="p-5 text-xs text-gray-600">{{ $f->lignes->count() }} ligne(s)</td>
                            <td class="p-5 text-center">
                                <a href="{{ route('patient.examens.show', $f) }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-widest"><i class="fas fa-eye"></i> Voir</a>
                                <a href="{{ route('patient.examens.print', $f) }}" target="_blank" class="ml-3 text-gray-500 hover:text-gray-700 text-xs font-bold uppercase tracking-widest"><i class="fas fa-print"></i> Imprimer</a>
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

@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
