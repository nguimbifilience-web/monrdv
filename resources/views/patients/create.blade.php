@extends('layouts.master')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('patients.index') }}" class="text-gray-400 hover:text-blue-900 font-bold text-xs uppercase flex items-center gap-2 transition-colors">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-50">
        <div class="bg-blue-600 py-6 px-8">
            <h4 class="text-white text-xl font-bold tracking-widest uppercase">Nouveau Patient</h4>
        </div>

        <div class="p-10">
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl">
                    @foreach ($errors->all() as $error)
                        <p class="text-xs font-bold">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form id="patientForm" action="{{ route('patients.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nom</label>
                        <input type="text" name="nom" id="input_nom" value="{{ old('nom') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Ex: NDONG">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Prénom</label>
                        <input type="text" name="prenom" id="input_prenom" value="{{ old('prenom') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Ex: Paul">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Téléphone</label>
                        <input type="text" name="telephone" id="input_telephone" value="{{ old('telephone') }}" required
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Ex: 074 00 00 00">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Email (Optionnel — pour créer un compte patient)</label>
                        <div class="relative">
                            <input type="email" name="email" id="input_email" value="{{ old('email') }}"
                                oninput="verifierEmail()"
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="patient@email.com">
                            <span id="emailStatus" class="absolute right-4 top-1/2 -translate-y-1/2 text-sm hidden"></span>
                        </div>
                        <p id="emailMsg" class="text-[10px] font-bold mt-1 hidden"></p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Quartier</label>
                    <input type="text" name="quartier" value="{{ old('quartier') }}"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all" placeholder="Quartier...">
                </div>

                {{-- Assurance --}}
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 mb-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Le patient est-il assuré ?</label>
                            <select name="est_assure" id="est_assure_select" onchange="toggleAssurance(this.value)"
                                class="w-full bg-white border-2 border-blue-200 rounded-2xl p-4 font-bold text-blue-900 focus:ring-0">
                                <option value="0" {{ old('est_assure') == '0' ? 'selected' : '' }}>Non</option>
                                <option value="1" {{ old('est_assure') == '1' ? 'selected' : '' }}>Oui</option>
                            </select>
                        </div>
                        <div id="bloc_assurance" style="display: none;">
                            <label class="block text-[10px] font-black text-blue-600 uppercase mb-2">Assurance</label>
                            <select name="assurance_id" class="w-full bg-white border-2 border-blue-200 rounded-2xl p-4 font-bold text-blue-900 focus:ring-0">
                                <option value="">-- Sélectionner --</option>
                                @foreach($assurances as $a)
                                    <option value="{{ $a->id }}" {{ old('assurance_id') == $a->id ? 'selected' : '' }}>{{ $a->nom }} ({{ $a->taux_couverture }}%)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Médecin traitant</label>
                    <select name="medecin_id" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl p-4 font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
                        <option value="">-- Aucun médecin assigné --</option>
                        @foreach($medecins as $m)
                            <option value="{{ $m->id }}" {{ old('medecin_id') == $m->id ? 'selected' : '' }}>Dr. {{ $m->nom }} {{ $m->prenom }} — {{ $m->specialite->nom ?? 'Généraliste' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Champ caché pour le code de validation -->
                <input type="hidden" name="validation_code" id="validation_code_input">

                <div class="flex gap-4">
                    <button type="button" onclick="demanderCode()" id="btnSubmit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-10 py-4 rounded-2xl shadow-lg transition-all uppercase tracking-widest text-xs">
                        <i class="fas fa-check-circle mr-2"></i> Enregistrer le patient
                    </button>
                    <a href="{{ route('patients.index') }}" class="py-4 px-6 text-gray-400 font-bold hover:text-red-500 uppercase text-[10px]">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de code de validation -->
<div id="codeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 py-5 px-8">
            <h3 class="text-white font-black text-lg uppercase tracking-widest">
                <i class="fas fa-shield-alt mr-2"></i> Validation requise
            </h3>
        </div>
        <div class="p-8">
            {{-- Étape 1 : Générer le code --}}
            <div id="codeStep1">
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6">
                    <p class="text-sm text-blue-800 font-bold">
                        <i class="fas fa-info-circle mr-2"></i>
                        Un code de validation à 6 chiffres va être généré. Communiquez-le à l'administrateur pour confirmer la création.
                    </p>
                </div>
                <p class="text-gray-500 text-xs font-bold mb-6">
                    Patient : <span id="modalPatientName" class="text-blue-900"></span>
                </p>
                <button onclick="envoyerCode()" id="btnEnvoyerCode"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-4 rounded-2xl uppercase tracking-widest text-xs transition-all shadow-lg">
                    <i class="fas fa-key mr-2"></i> Générer le code
                </button>
            </div>

            {{-- Étape 2 : Afficher le code + saisie --}}
            <div id="codeStep2" class="hidden">
                {{-- Affichage du code généré --}}
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 border-2 border-orange-200 rounded-2xl p-5 mb-6 text-center">
                    <p class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-2">Code de validation</p>
                    <div class="flex items-center justify-center gap-3">
                        <span id="codeDisplay" class="text-4xl font-black text-orange-600 tracking-[0.4em] font-mono"></span>
                        <button onclick="copierCode()" id="btnCopier" title="Copier le code"
                            class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-xl flex items-center justify-center transition-all shadow-md">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p id="copieMsg" class="text-green-600 text-[10px] font-bold mt-2 hidden">
                        <i class="fas fa-check mr-1"></i> Code copié !
                    </p>
                    <p class="text-orange-400 text-[10px] mt-2">
                        <i class="fas fa-clock mr-1"></i> Expire dans 10 minutes
                    </p>
                </div>

                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Entrez le code pour confirmer</label>
                <div class="flex gap-2 mb-6 justify-center">
                    <input type="text" id="code1" maxlength="1" class="w-14 h-14 text-center text-2xl font-black text-blue-900 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-0" oninput="nextDigit(1)">
                    <input type="text" id="code2" maxlength="1" class="w-14 h-14 text-center text-2xl font-black text-blue-900 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-0" oninput="nextDigit(2)">
                    <input type="text" id="code3" maxlength="1" class="w-14 h-14 text-center text-2xl font-black text-blue-900 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-0" oninput="nextDigit(3)">
                    <input type="text" id="code4" maxlength="1" class="w-14 h-14 text-center text-2xl font-black text-blue-900 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-0" oninput="nextDigit(4)">
                    <input type="text" id="code5" maxlength="1" class="w-14 h-14 text-center text-2xl font-black text-blue-900 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-0" oninput="nextDigit(5)">
                    <input type="text" id="code6" maxlength="1" class="w-14 h-14 text-center text-2xl font-black text-blue-900 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:bg-white focus:ring-0" oninput="nextDigit(6)">
                </div>
                <p id="codeError" class="text-red-500 text-xs font-bold mb-4 hidden text-center"></p>
                <button onclick="validerCode()" id="btnValiderCode"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl uppercase tracking-widest text-xs transition-all shadow-lg">
                    <i class="fas fa-check mr-2"></i> Valider et créer le patient
                </button>
                <button onclick="envoyerCode()" class="w-full mt-3 text-orange-500 hover:text-orange-600 font-bold text-xs uppercase tracking-widest transition-colors">
                    <i class="fas fa-redo mr-1"></i> Regénérer un nouveau code
                </button>
            </div>

            <button onclick="fermerModal()" class="w-full mt-4 text-gray-400 hover:text-red-500 font-bold text-xs uppercase tracking-widest transition-colors">
                Annuler
            </button>
        </div>
    </div>
</div>

<script>
function toggleAssurance(val) {
    document.getElementById('bloc_assurance').style.display = (val === "1") ? "block" : "none";
}
window.onload = function() {
    toggleAssurance(document.getElementById('est_assure_select').value);
};

let emailTimer;
function verifierEmail() {
    clearTimeout(emailTimer);
    const email = document.getElementById('input_email').value;
    const status = document.getElementById('emailStatus');
    const msg = document.getElementById('emailMsg');

    if (!email || !email.includes('@')) {
        status.classList.add('hidden');
        msg.classList.add('hidden');
        return;
    }

    emailTimer = setTimeout(() => {
        fetch(`/api/patients/check-email?email=${encodeURIComponent(email)}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            status.classList.remove('hidden');
            msg.classList.remove('hidden');

            if (data.exists) {
                status.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                msg.textContent = 'Cet email est déjà utilisé';
                msg.className = 'text-[10px] font-bold mt-1 text-red-500';
            } else {
                status.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
                msg.textContent = 'Email disponible';
                msg.className = 'text-[10px] font-bold mt-1 text-green-500';
            }
        });
    }, 500);
}

// === Système de validation par code ===

let generatedCode = '';

function demanderCode() {
    const nom = document.getElementById('input_nom').value;
    const prenom = document.getElementById('input_prenom').value;
    const telephone = document.getElementById('input_telephone').value;

    if (!nom || !prenom || !telephone) {
        alert('Veuillez remplir les champs obligatoires (Nom, Prénom, Téléphone).');
        return;
    }

    document.getElementById('modalPatientName').textContent = prenom + ' ' + nom;
    document.getElementById('codeStep1').classList.remove('hidden');
    document.getElementById('codeStep2').classList.add('hidden');
    document.getElementById('codeModal').classList.remove('hidden');
    document.getElementById('codeModal').classList.add('flex');
}

function envoyerCode() {
    const btn = document.getElementById('btnEnvoyerCode');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Génération...';

    const formData = new FormData();
    formData.append('nom', document.getElementById('input_nom').value);
    formData.append('prenom', document.getElementById('input_prenom').value);
    formData.append('telephone', document.getElementById('input_telephone').value);
    const email = document.getElementById('input_email').value;
    if (email) formData.append('email', email);
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    fetch('{{ route("patients.send-code") }}', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            generatedCode = data.code;
            document.getElementById('codeDisplay').textContent = data.code;
            document.getElementById('codeStep1').classList.add('hidden');
            document.getElementById('codeStep2').classList.remove('hidden');
            // Réinitialiser les champs de saisie
            for (let i = 1; i <= 6; i++) {
                document.getElementById('code' + i).value = '';
            }
            document.getElementById('code1').focus();
        }
    })
    .catch(() => {
        alert('Erreur lors de la génération du code.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-key mr-2"></i> Générer le code';
    });
}

function copierCode() {
    navigator.clipboard.writeText(generatedCode).then(() => {
        const msg = document.getElementById('copieMsg');
        msg.classList.remove('hidden');
        setTimeout(() => msg.classList.add('hidden'), 2000);
    });
}

function nextDigit(current) {
    const input = document.getElementById('code' + current);
    input.value = input.value.replace(/[^0-9]/g, '');
    if (input.value && current < 6) {
        document.getElementById('code' + (current + 1)).focus();
    }
}

function validerCode() {
    let code = '';
    for (let i = 1; i <= 6; i++) {
        code += document.getElementById('code' + i).value;
    }

    if (code.length !== 6) {
        document.getElementById('codeError').textContent = 'Veuillez saisir les 6 chiffres du code.';
        document.getElementById('codeError').classList.remove('hidden');
        return;
    }

    document.getElementById('validation_code_input').value = code;
    document.getElementById('patientForm').submit();
}

function fermerModal() {
    document.getElementById('codeModal').classList.add('hidden');
    document.getElementById('codeModal').classList.remove('flex');
    generatedCode = '';
    for (let i = 1; i <= 6; i++) {
        document.getElementById('code' + i).value = '';
    }
}

// Permettre le collage du code complet
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('code1').addEventListener('paste', function(e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\s/g, '');
        if (/^\d{6}$/.test(pasted)) {
            for (let i = 0; i < 6; i++) {
                document.getElementById('code' + (i + 1)).value = pasted[i];
            }
            document.getElementById('code6').focus();
        }
    });
});
</script>
@endsection
