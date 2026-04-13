@extends('layouts.master')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Configuration système</h1>
    <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Paramètres généraux de l'application</p>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg"><p class="text-xs font-bold">{{ session('success') }}</p></div>
@endif

<form action="{{ route('superadmin.settings.system.update') }}" method="POST">
    @csrf @method('PUT')

    @php
        $groupLabels = [
            'general' => ['Général', 'fa-cog', 'bg-blue-500'],
            'security' => ['Sécurité', 'fa-shield-alt', 'bg-red-500'],
            'notifications' => ['Notifications', 'fa-envelope', 'bg-green-500'],
            'maintenance' => ['Maintenance', 'fa-tools', 'bg-yellow-500'],
        ];
    @endphp

    <div class="space-y-4 md:space-y-6">
        @foreach($groupLabels as $groupKey => [$label, $icon, $color])
            @if(isset($settings[$groupKey]))
                <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
                    <div class="flex items-center gap-3 p-5 md:p-6 border-b border-gray-50">
                        <div class="w-10 h-10 {{ $color }} rounded-xl flex items-center justify-center text-white">
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <h2 class="font-black text-blue-900 text-base md:text-lg uppercase">{{ $label }}</h2>
                    </div>
                    <div class="p-5 md:p-6 space-y-4">
                        @foreach($settings[$groupKey] as $setting)
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                    {{ $setting->label ?? $setting->key }}
                                </label>
                                @if($setting->type === 'bool')
                                    <label class="flex items-center gap-3 text-xs font-bold text-blue-900">
                                        <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                        <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" {{ $setting->value ? 'checked' : '' }} class="w-5 h-5 accent-blue-600">
                                        Activé
                                    </label>
                                @elseif($setting->type === 'int')
                                    <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full sm:max-w-xs bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                                @else
                                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-blue-500 focus:ring-0">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 md:px-8 py-3 md:py-4 rounded-2xl shadow-lg uppercase tracking-widest text-xs">
            <i class="fas fa-save mr-2"></i>Enregistrer les paramètres
        </button>
    </div>
</form>
@endsection
