@extends('layouts.master')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-black uppercase italic" style="color: var(--mr-text-primary)">Apparence</h1>
    <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase tracking-widest">Personnalisez les couleurs appliquées à toutes les pages</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
        <p class="text-xs font-bold">Chaque couleur doit être au format #RRGGBB (ex : #f1f5f9).</p>
    </div>
@endif

<form action="{{ route('superadmin.settings.appearance.update') }}" method="POST">
    @csrf @method('PUT')

    <div class="mr-card rounded-2xl md:rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center gap-3 p-5 md:p-6 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background-color: var(--mr-accent)">
                <i class="fas fa-palette"></i>
            </div>
            <h2 class="font-black text-base md:text-lg uppercase" style="color: var(--mr-text-primary)">Palette globale</h2>
        </div>

        <div class="p-5 md:p-6 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            @php
                $fields = [
                    'theme_bg_page'      => ['Fond des pages',       'Couleur derrière les cartes'],
                    'theme_bg_card'      => ['Fond des cartes',      'Couleur des blocs et formulaires'],
                    'theme_text_primary' => ['Texte principal',      'Titres et libellés forts'],
                    'theme_accent'       => ['Couleur d\'accent',    'Boutons, liens et éléments actifs'],
                    'theme_sidebar_bg'   => ['Fond de la sidebar',   'Barre latérale Super Admin'],
                ];
            @endphp
            @foreach($fields as $key => [$label, $desc])
                <div class="border border-gray-200 rounded-xl p-4">
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">{{ $label }}</label>
                    <p class="text-[10px] text-gray-400 mb-3">{{ $desc }}</p>
                    <div class="flex items-center gap-3">
                        <input type="color" name="{{ $key }}" value="{{ $theme[$key] }}"
                               class="w-14 h-12 rounded-lg border-2 border-gray-200 cursor-pointer"
                               oninput="document.getElementById('{{ $key }}_hex').value = this.value">
                        <input type="text" id="{{ $key }}_hex" value="{{ $theme[$key] }}"
                               pattern="^#[0-9a-fA-F]{6}$" maxlength="7"
                               class="flex-1 bg-gray-50 border-2 border-gray-200 rounded-xl p-3 font-mono text-sm font-bold uppercase focus:border-blue-500 focus:ring-0"
                               oninput="document.querySelector('input[name={{ $key }}]').value = this.value">
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-5 md:p-6 border-t border-gray-100 flex flex-col sm:flex-row gap-3 sm:justify-between sm:items-center">
            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                <i class="fas fa-info-circle mr-1"></i>
                Les couleurs s'appliquent immédiatement après enregistrement.
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="resetDefaults()"
                        class="border-2 border-gray-200 text-gray-600 font-black px-5 py-3 rounded-2xl uppercase tracking-widest text-xs hover:bg-gray-50">
                    <i class="fas fa-undo mr-1"></i>Valeurs par défaut
                </button>
                <button type="submit" class="text-white font-black px-6 md:px-8 py-3 md:py-4 rounded-2xl shadow-lg uppercase tracking-widest text-xs"
                        style="background-color: var(--mr-accent)">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function resetDefaults() {
        const defaults = {
            theme_bg_page: '#f1f5f9',
            theme_bg_card: '#ffffff',
            theme_text_primary: '#1e3a8a',
            theme_accent: '#2563eb',
            theme_sidebar_bg: '#111827'
        };
        for (const [k, v] of Object.entries(defaults)) {
            document.querySelector(`input[name=${k}]`).value = v;
            document.getElementById(`${k}_hex`).value = v;
        }
    }
</script>
@endsection
