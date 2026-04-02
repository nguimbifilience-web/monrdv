<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $clinic->name }} - Compte suspendu</title>
    <style>{!! file_get_contents(public_path('css/app.css')) !!}</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl overflow-hidden">

        {{-- Bandeau rouge --}}
        <div class="bg-red-600 p-8 text-center">
            @if($clinic->logo_url)
                <img src="{{ $clinic->logo_url }}" alt="{{ $clinic->name }}" class="w-16 h-16 rounded-2xl object-cover mx-auto mb-4 border-2 border-white/20">
            @else
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-black text-xl">{{ strtoupper(substr($clinic->name, 0, 2)) }}</span>
                </div>
            @endif
            <h1 class="text-white font-black text-2xl uppercase tracking-tight">{{ $clinic->name }}</h1>
            <div class="mt-3">
                <span class="bg-white/20 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">
                    <i class="fas fa-ban mr-1"></i> Compte suspendu
                </span>
            </div>
        </div>

        {{-- Contenu --}}
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>

            <h2 class="text-xl font-black text-gray-800 mb-3">Accès suspendu</h2>

            <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                L'accès à votre clinique a été temporairement suspendu.
                Veuillez contacter l'administrateur de la plateforme pour régulariser votre situation.
            </p>

            @if($clinic->blocked_reason)
            <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-6 text-left">
                <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Motif</p>
                <p class="text-sm text-red-700 font-medium">{{ $clinic->blocked_reason }}</p>
            </div>
            @endif

            @if($clinic->blocked_at)
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-6">
                Suspendu depuis le {{ $clinic->blocked_at->format('d/m/Y') }}
            </p>
            @endif

            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-2 bg-gray-100 text-gray-600 px-6 py-3 rounded-xl font-bold text-xs uppercase hover:bg-gray-200 transition-all">
                <i class="fas fa-arrow-left"></i> Retour à la connexion
            </a>
        </div>
    </div>

</body>
</html>
