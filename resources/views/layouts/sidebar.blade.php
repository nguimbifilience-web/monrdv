<div class="w-64 bg-blue-900 h-screen flex flex-col shadow-2xl">
    <div class="p-8">
        <h1 class="text-white font-black text-2xl uppercase tracking-tighter italic">
            Mon<span class="text-orange-500">RDV</span>
        </h1>
        <p class="text-blue-400 text-[10px] uppercase font-bold tracking-widest mt-1">
            Gestion Médicale
        </p>
    </div>

    <nav class="flex-1 px-4 space-y-2">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-4 px-6 py-4 rounded-2xl 
           {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-th-large"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Dashboard</span>
        </a>

        <!-- Patients -->
        <a href="{{ route('patients.index') }}" 
           class="flex items-center gap-4 px-6 py-4 rounded-2xl 
           {{ request()->routeIs('patients.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-injured"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Patients</span>
        </a>

        <!-- Médecins -->
        <a href="{{ route('medecins.index') }}" 
           class="flex items-center gap-4 px-6 py-4 rounded-2xl 
           {{ request()->routeIs('medecins.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-md"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Médecins</span>
        </a>

        <!-- Assurances -->
        <a href="{{ route('assurances.index') }}" 
           class="flex items-center gap-4 px-6 py-4 rounded-2xl 
           {{ request()->routeIs('assurances.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-shield-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Assurances</span>
        </a>

        <!-- Spécialités -->
        <a href="{{ route('specialites.index') }}" 
           class="flex items-center gap-4 px-6 py-4 rounded-2xl 
           {{ request()->routeIs('specialites.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-stethoscope"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Spécialités</span>
        </a>

    </nav>

    <div class="p-8 border-t border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center text-white text-xs font-black">
                AD
            </div>
            <div>
                <p class="text-white text-[10px] font-black uppercase tracking-tight">Admin</p>
                <p class="text-blue-400 text-[9px] italic">Connecté</p>
            </div>
        </div>
    </div>
</div>