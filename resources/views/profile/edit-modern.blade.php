@extends('layouts.master')

@section('content')
<div class="max-w-2xl mx-auto py-6">

    <div class="mb-6">
        <h1 class="text-2xl font-black text-blue-900 uppercase tracking-tighter">Mon Profil</h1>
        <p class="text-gray-400 italic text-sm">Modifiez vos informations personnelles et votre mot de passe.</p>
    </div>

    @if(session('success'))
    <div id="flash-msg" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-2xl shadow-sm flex items-center">
        <i class="fas fa-check-circle mr-3"></i>
        <span class="font-medium text-sm">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-2xl">
        <ul class="text-xs text-red-600 font-bold list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Informations personnelles --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 p-8 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black" style="background-color: var(--clinic-secondary)">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="font-black text-blue-900 uppercase text-sm">Informations personnelles</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $user->role }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-blue-800 transition-all">
                    <i class="fas fa-save mr-1"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>

    {{-- Changement de mot de passe --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-500">
                <i class="fas fa-lock"></i>
            </div>
            <div>
                <h2 class="font-black text-blue-900 uppercase text-sm">Changer le mot de passe</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Minimum 8 caracteres</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Mot de passe actuel</label>
                <input type="password" name="current_password" required
                    class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all"
                    placeholder="Votre mot de passe actuel">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nouveau mot de passe</label>
                    <input type="password" name="password" required
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all"
                        placeholder="Min. 8 caracteres">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Confirmer</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:ring-0 transition-all"
                        placeholder="Retapez le mot de passe">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-orange-600 transition-all">
                    <i class="fas fa-key mr-1"></i> Changer le mot de passe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
