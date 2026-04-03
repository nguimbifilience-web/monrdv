<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($clinic ?? null)?->name ?? 'MonRDV' }} - Inscription Patient</title>
    <style>{!! file_get_contents(public_path('css/app.css')) !!}</style>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-esm.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden"
    style="background: linear-gradient(135deg, {{ ($clinic ?? null)?->getPrimaryColorOrDefault() ?? '#1e3a8a' }}, {{ ($clinic ?? null)?->getPrimaryColorOrDefault() ?? '#1e3a8a' }}dd, {{ ($clinic ?? null)?->getPrimaryColorOrDefault() ?? '#1e3a8a' }})">

    <div class="w-full max-w-4xl flex rounded-[2.5rem] shadow-2xl overflow-hidden relative z-10">

        {{-- PANNEAU GAUCHE --}}
        <div class="hidden md:flex w-1/2 bg-blue-950 p-12 flex-col justify-between relative overflow-hidden">
            <div class="absolute top-[-50px] right-[-50px] w-40 h-40 bg-cyan-400/10 rounded-full"></div>
            <div class="absolute bottom-[-30px] left-[-30px] w-32 h-32 bg-orange-500/10 rounded-full"></div>

            <div>
                <div class="flex items-center gap-4 mb-2">
                    @if(($clinic ?? null)?->logo_url)
                        <img src="{{ $clinic->logo_url }}" alt="{{ $clinic->name }}" class="w-14 h-14 rounded-2xl object-cover shadow-lg">
                    @else
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg"
                            style="background-color: {{ ($clinic ?? null)?->getSecondaryColorOrDefault() ?? '#f97316' }}">
                            <span class="text-white font-black text-lg italic tracking-tighter">{{ strtoupper(substr(($clinic ?? null)?->name ?? 'MR', 0, 2)) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-white font-black text-3xl uppercase tracking-tighter italic">
                            {{ ($clinic ?? null)?->name ?? 'MonRDV' }}
                        </h1>
                        <p class="text-blue-400 text-[10px] uppercase font-bold tracking-widest">Espace Patient — <span class="text-orange-400">MnRdv</span></p>
                    </div>
                </div>
            </div>

            <div class="space-y-6 relative z-10">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-cyan-400/20 rounded-xl flex items-center justify-center text-cyan-400 shrink-0 mt-1">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-black text-sm uppercase">Prenez RDV en ligne</h3>
                        <p class="text-blue-300 text-xs mt-1">Choisissez votre medecin et reservez un creneau en quelques clics.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-400/20 rounded-xl flex items-center justify-center text-green-400 shrink-0 mt-1">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-black text-sm uppercase">Gerez vos RDV</h3>
                        <p class="text-blue-300 text-xs mt-1">Consultez, modifiez ou annulez vos rendez-vous a tout moment.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-orange-400/20 rounded-xl flex items-center justify-center text-orange-400 shrink-0 mt-1">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-black text-sm uppercase">Espace securise</h3>
                        <p class="text-blue-300 text-xs mt-1">Vos donnees medicales sont protegees et confidentielles.</p>
                    </div>
                </div>
            </div>

            <p class="text-blue-500 text-[9px] uppercase font-bold tracking-widest">&copy; {{ date('Y') }} {{ ($clinic ?? null)?->name ?? 'MonRDV' }} — MnRdv</p>
        </div>

        {{-- PANNEAU DROIT - FORMULAIRE --}}
        <div class="w-full md:w-1/2 bg-white p-10 md:p-12 flex flex-col justify-center relative overflow-hidden">

            <div class="md:hidden text-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-3">
                    <span class="text-white font-black text-xl italic tracking-tighter">LZy</span>
                </div>
                <h1 class="text-blue-900 font-black text-3xl uppercase tracking-tighter italic">{{ ($clinic ?? null)?->name ?? 'MonRDV' }} <span class="text-orange-500 text-lg">MnRdv</span></h1>
            </div>

            <div class="mb-6 relative z-10">
                <h2 class="text-2xl font-black text-blue-900 uppercase italic">Inscription</h2>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Creez votre compte patient</p>
            </div>

            @if($noClinic ?? false)
            <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-xl relative z-10">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    <span class="text-yellow-700 text-xs font-bold">Pour vous inscrire, utilisez le lien d'acces de votre clinique.</span>
                </div>
                <p class="text-yellow-600 text-[10px] mt-1">Demandez le lien a votre clinique (ex: {{ url('/c/nom-clinique') }})</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl relative z-10">
                @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                    <span class="text-red-600 text-xs font-bold">{{ $error }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4 relative z-10">
                @csrf
                @if(($clinic ?? null)?->slug)
                    <input type="hidden" name="clinic_slug" value="{{ $clinic->slug }}">
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all"
                            placeholder="NDONG">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Prenom</label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all"
                            placeholder="Paul">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Telephone</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-300"><i class="fas fa-phone"></i></span>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-12 pr-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all"
                            placeholder="074 00 00 00">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-300"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-12 pr-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all"
                            placeholder="patient@email.com">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Mot de passe</label>
                        <input type="password" name="password" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all"
                            placeholder="********">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Confirmer</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-4 py-3 font-bold text-blue-900 text-sm focus:border-cyan-400 focus:bg-white focus:ring-0 transition-all"
                            placeholder="********">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-900 text-white py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-blue-800 shadow-lg shadow-blue-900/30 transition-all hover:shadow-xl hover:-translate-y-0.5">
                    <i class="fas fa-user-plus mr-2"></i> Creer mon compte
                </button>

                <p class="text-center text-xs text-gray-400 font-bold">
                    Deja inscrit ?
                    <a href="{{ route('login', ($clinic ?? null)?->slug ? ['clinic' => $clinic->slug] : []) }}" class="text-cyan-500 hover:text-cyan-600 transition-colors">Se connecter</a>
                </p>
            </form>
        </div>
    </div>

</body>
</html>
