@extends('layouts.master')

@section('content')
<div class="p-8 max-w-4xl">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-blue-900 uppercase italic">Nouvelle ordonnance</h1>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Redaction d'une prescription</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('medecin.ordonnances.store') }}" class="bg-white rounded-[2rem] shadow-sm border border-gray-50 p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Patient</label>
                <select name="patient_id" required class="w-full border-gray-200 rounded-xl text-sm">
                    <option value="">-- Choisir --</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ (string)$patientId === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} {{ $p->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Date</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full border-gray-200 rounded-xl text-sm">
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Notes generales (optionnel)</label>
            <textarea name="notes_generales" rows="2" class="w-full border-gray-200 rounded-xl text-sm">{{ old('notes_generales') }}</textarea>
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500">Medicaments</label>
                <button type="button" onclick="addLigne()" class="text-blue-600 text-xs font-bold uppercase tracking-widest"><i class="fas fa-plus"></i> Ajouter</button>
            </div>
            <div id="lignes" class="space-y-3"></div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('medecin.ordonnances.index') }}" class="px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-widest bg-gray-100 hover:bg-gray-200">Annuler</a>
            <button type="submit" class="px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-100">Enregistrer</button>
        </div>
    </form>
</div>

<template id="ligne-tpl">
    <div class="grid grid-cols-12 gap-2 items-center border border-gray-100 rounded-xl p-3">
        <input type="text" name="lignes[__IDX__][medicament]" placeholder="Medicament *" required class="col-span-4 border-gray-200 rounded-lg text-sm">
        <input type="text" name="lignes[__IDX__][posologie]" placeholder="Posologie" class="col-span-3 border-gray-200 rounded-lg text-sm">
        <input type="text" name="lignes[__IDX__][duree]" placeholder="Duree" class="col-span-2 border-gray-200 rounded-lg text-sm">
        <input type="text" name="lignes[__IDX__][quantite]" placeholder="Quantite" class="col-span-2 border-gray-200 rounded-lg text-sm">
        <button type="button" onclick="this.closest('.grid').remove()" class="col-span-1 text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
    </div>
</template>

<script>
    let idx = 0;
    function addLigne() {
        const tpl = document.getElementById('ligne-tpl').innerHTML.replaceAll('__IDX__', idx++);
        document.getElementById('lignes').insertAdjacentHTML('beforeend', tpl);
    }
    addLigne();
</script>
@endsection
