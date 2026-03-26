<div id="modalAdd" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-xl p-8 shadow-2xl">
        <h2 class="text-xl font-black text-blue-900 uppercase italic mb-6 border-b pb-4">Nouveau Partenaire</h2>
        
        <form action="{{ route('assurances.store') }}" method="POST" class="grid grid-cols-2 gap-4">
            @csrf
            <div class="col-span-2">
                <label class="text-[10px] font-black uppercase text-gray-400">Nom de l'Assurance</label>
                <input type="text" name="nom" class="w-full bg-gray-50 border-none rounded-xl p-3 font-bold" placeholder="ex: AXA, NSIA..." required>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400">Nom du Référent</label>
                <input type="text" name="nom_referent" class="w-full bg-gray-50 border-none rounded-xl p-3 font-bold" required>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400">Taux de Couverture (%)</label>
                <input type="number" name="taux_couverture" class="w-full bg-gray-50 border-none rounded-xl p-3 font-bold" required>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400">Téléphone</label>
                <input type="text" name="telephone" class="w-full bg-gray-50 border-none rounded-xl p-3">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-gray-400">Email</label>
                <input type="email" name="email" class="w-full bg-gray-50 border-none rounded-xl p-3">
            </div>

            <div class="col-span-2 flex justify-end gap-4 mt-6">
                <button type="button" onclick="toggleModal('modalAdd')" class="text-gray-400 font-bold uppercase text-xs">Annuler</button>
                <button type="submit" class="bg-cyan-400 text-white px-8 py-3 rounded-xl font-black uppercase text-[10px] shadow-lg">Créer le partenaire</button>
            </div>
        </form>
    </div>
</div>