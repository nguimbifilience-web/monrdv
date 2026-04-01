<div class="w-64 bg-gray-900 h-screen flex flex-col shadow-2xl">
    <div class="p-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30">
                <span class="text-white font-black text-sm italic tracking-tighter">LZy</span>
            </div>
            <div>
                <h1 class="text-white font-black text-2xl uppercase tracking-tighter italic">
                    Mon<span class="text-red-500">RDV</span>
                </h1>
                <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">
                    Super Admin
                </p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('dashboard') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-white/10' }} transition-all">
            <i class="fas fa-chart-line"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Dashboard</span>
        </a>
        <a href="{{ route('clinics.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('clinics.*') ? 'bg-red-500 text-white' : 'text-gray-300 hover:bg-white/10' }} transition-all">
            <i class="fas fa-hospital"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Cliniques</span>
        </a>
    </nav>

    <div class="p-8 border-t border-white/10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center text-white text-xs font-black">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400 text-[9px] italic">Super Admin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
