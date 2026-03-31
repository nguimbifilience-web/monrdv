<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonRDV - Connexion</title>
    <style>{!! file_get_contents(public_path('css/app.css')) !!}</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .watermark {
            position: absolute;
            font-size: 28rem;
            font-weight: 900;
            font-style: italic;
            color: rgba(255, 255, 255, 0.03);
            pointer-events: none;
            user-select: none;
            line-height: 1;
            letter-spacing: -0.05em;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            white-space: nowrap;
        }
        .watermark-small {
            position: absolute;
            font-size: 12rem;
            font-weight: 900;
            font-style: italic;
            color: rgba(255, 255, 255, 0.025);
            pointer-events: none;
            user-select: none;
            line-height: 1;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Filigrane LZy en fond --}}
    <div class="watermark">LZy</div>
    <div class="watermark-small" style="top: 8%; left: 5%; transform: rotate(-25deg);">LZy</div>
    <div class="watermark-small" style="bottom: 5%; right: 3%; transform: rotate(10deg); font-size: 8rem;">LZy</div>
    <div class="watermark-small" style="top: 15%; right: 8%; transform: rotate(-5deg); font-size: 6rem; color: rgba(255,255,255,0.02);">LZy</div>
    <div class="watermark-small" style="bottom: 20%; left: 10%; transform: rotate(20deg); font-size: 5rem; color: rgba(255,255,255,0.015);">LZy</div>

    <div class="w-full max-w-4xl flex rounded-[2.5rem] shadow-2xl overflow-hidden relative z-10">

        {{-- PANNEAU GAUCHE --}}
        <div class="hidden md:flex w-1/2 bg-blue-950 p-12 flex-col justify-between relative overflow-hidden">
            {{-- Cercles décoratifs --}}
            <div class="absolute top-[-50px] right-[-50px] w-40 h-40 bg-cyan-400/10 rounded-full"></div>
            <div class="absolute bottom-[-30px] left-[-30px] w-32 h-32 bg-orange-500/10 rounded-full"></div>
            <div class="absolute top-1/2 right-10 w-20 h-20 bg-blue-400/10 rounded-full"></div>

            {{-- Filigrane LZy dans le panneau --}}
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-[10rem] font-black italic text-white/[0.03] pointer-events-none select-none rotate-[-20deg]">LZy</div>

            <div>
                {{-- Logo LZy + MonRDV --}}
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                        <span class="text-white font-black text-lg italic tracking-tighter">LZy</span>
                    </div>
                    <div>
                        <h1 class="text-white font-black text-3xl uppercase tracking-tighter italic">
                            Mon<span class="text-orange-500">RDV</span>
                        </h1>
                        <p class="text-blue-400 text-[10px] uppercase font-bold tracking-widest">
                            Gestion Médicale
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-6 relative z-10">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-cyan-400/20 rounded-xl flex items-center justify-center text-cyan-400 shrink-0 mt-1">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-black text-sm uppercase">Rendez-vous</h3>
                        <p class="text-blue-300 text-xs mt-1">Planifiez et gérez les consultations de vos patients en toute simplicité.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-400/20 rounded-xl flex items-center justify-center text-green-400 shrink-0 mt-1">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-black text-sm uppercase">Médecins</h3>
                        <p class="text-blue-300 text-xs mt-1">Gérez votre équipe de praticiens et leur planning mensuel.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-orange-400/20 rounded-xl flex items-center justify-center text-orange-400 shrink-0 mt-1">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-black text-sm uppercase">Assurances</h3>
                        <p class="text-blue-300 text-xs mt-1">Suivi des assurances, taux de couverture et facturation automatique.</p>
                    </div>
                </div>
            </div>

            <p class="text-blue-500 text-[9px] uppercase font-bold tracking-widest">
                &copy; {{ date('Y') }} MonRDV — LZy
            </p>
        </div>

        {{-- PANNEAU DROIT - FORMULAIRE --}}
        <div class="w-full md:w-1/2 bg-white p-10 md:p-12 flex flex-col justify-center relative overflow-hidden">
            {{-- Filigrane LZy léger dans le formulaire --}}
            <div class="absolute bottom-[-20px] right-[-10px] text-[8rem] font-black italic text-gray-100 pointer-events-none select-none">LZy</div>

            {{-- Logo mobile --}}
            <div class="md:hidden text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-3">
                    <span class="text-white font-black text-xl italic tracking-tighter">LZy</span>
                </div>
                <h1 class="text-blue-900 font-black text-3xl uppercase tracking-tighter italic">
                    Mon<span class="text-orange-500">RDV</span>
                </h1>
            </div>

            <div class="mb-8 relative z-10">
                <h2 class="text-2xl font-black text-blue-900 uppercase italic">Connexion</h2>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Accédez à votre espace</p>
            </div>

            {{-- Erreurs --}}
            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl relative z-10">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                    <span class="text-red-600 text-xs font-bold">{{ $errors->first() }}</span>
                </div>
            </div>
            @endif

            @if(session('status'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl relative z-10">
                <span class="text-green-600 text-xs font-bold">{{ session('status') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5 relative z-10">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Adresse email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-300">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-12 pr-4 py-4 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all placeholder-gray-300"
                            placeholder="admin@monrdv.ga">
                    </div>
                </div>

                {{-- Mot de passe --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Mot de passe</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-300">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password_input" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-12 pr-12 py-4 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all placeholder-gray-300"
                            placeholder="Votre mot de passe">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-300 hover:text-cyan-400 transition-colors">
                            <i class="fas fa-eye" id="eye_icon"></i>
                        </button>
                    </div>
                </div>

                {{-- Se souvenir / Mot de passe oublié --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-cyan-400 focus:ring-cyan-400">
                        <span class="text-xs font-bold text-gray-400">Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-cyan-500 hover:text-cyan-600 transition-colors">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>

                {{-- Bouton connexion --}}
                <button type="submit" class="w-full bg-blue-900 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-blue-800 shadow-lg shadow-blue-900/30 transition-all hover:shadow-xl hover:-translate-y-0.5">
                    <i class="fas fa-sign-in-alt mr-2"></i> Se connecter
                </button>

                <p class="text-center text-xs text-gray-400 font-bold mt-4">
                    Vous êtes patient ? Contactez le secrétariat pour créer votre compte.
                </p>
            </form>
        </div>
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('password_input');
        const icon = document.getElementById('eye_icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    </script>

</body>
</html>
