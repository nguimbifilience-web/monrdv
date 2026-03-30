<div class="w-64 bg-blue-900 h-screen flex flex-col shadow-2xl">
    <div class="p-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                <span class="text-white font-black text-sm italic tracking-tighter">LZy</span>
            </div>
            <div>
                <h1 class="text-white font-black text-2xl uppercase tracking-tighter italic">
                    Mon<span class="text-orange-500">RDV</span>
                </h1>
                <p class="text-green-400 text-[10px] uppercase font-bold tracking-widest">Espace Medecin</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2">

        <a href="{{ route('medecin.dashboard') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.dashboard') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-th-large"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Accueil</span>
        </a>

        <a href="{{ route('medecin.planning') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.planning') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mon Planning</span>
        </a>

        <a href="{{ route('medecin.rendezvous') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.rendezvous') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-check"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mes Rendez-vous</span>
        </a>

        <a href="{{ route('medecin.patients') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.patients') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-injured"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mes Patients</span>
        </a>

    </nav>

    <div class="p-8 border-t border-white/10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white text-xs font-black">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">{{ auth()->user()->name }}</p>
                    <p class="text-blue-400 text-[9px] italic">Medecin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-400 hover:text-red-400 transition-colors" title="Deconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
