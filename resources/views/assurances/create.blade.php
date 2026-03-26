@extends('layouts.master')

@section('content')
<div class="max-w-2xl mx-auto mt-10 px-4">
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('assurances.index') }}" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Nouveau Partenaire</h1>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Enregistrement Assurance</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-8 md:p-12">
        <form action="{{ route('assurances.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="group">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-indigo-600 transition-colors text-center">Nom de l'organisme</label>
                <input type="text" name="nom" required 
                       class="w-full bg-slate-50 border-none rounded-2xl py-5 px-6 text-slate-900 font-bold placeholder:text-slate-300 focus:ring-4 focus:ring-indigo-50 transition-all outline-none text-center text-xl shadow-inner"
                       placeholder="CNAMGS, ASCOMA...">
            </div>

            <div class="group">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 group-focus-within:text-indigo-600 transition-colors">Description / Notes</label>
                <textarea name="description" rows="3" 
                          class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-slate-900 font-medium placeholder:text-slate-300 focus:ring-4 focus:ring-indigo-50 transition-all outline-none shadow-inner resize-none"
                          placeholder="Détails sur la prise en charge..."></textarea>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-slate-900/20 hover:bg-indigo-600 transition-all mt-4">
                Confirmer l'ajout
            </button>
        </form>
    </div>
</div>
@endsection