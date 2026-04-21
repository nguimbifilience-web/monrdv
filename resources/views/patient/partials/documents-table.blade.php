<table class="w-full text-left">
    <thead class="bg-gray-50/50 border-b border-gray-50">
        <tr class="text-[9px] font-black uppercase text-gray-300">
            <th class="p-5">Document</th>
            <th class="p-5">Type</th>
            <th class="p-5">Date d'ajout</th>
            <th class="p-5 text-center">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
        @forelse($documents as $doc)
        <tr class="hover:bg-gray-50/30 transition-colors">
            <td class="p-5">
                <div class="flex items-center gap-3">
                    @if(str_ends_with($doc->fichier, '.pdf'))
                        <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-500"><i class="fas fa-file-pdf text-lg"></i></div>
                    @else
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500"><i class="fas fa-file-image text-lg"></i></div>
                    @endif
                    <span class="font-black text-blue-900 text-xs">{{ $doc->nom }}</span>
                </div>
            </td>
            <td class="p-5">
                @if($doc->type === 'assurance')
                    <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase"><i class="fas fa-shield-alt mr-1"></i>Assurance</span>
                @elseif($doc->type === 'ordonnance')
                    <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase"><i class="fas fa-prescription mr-1"></i>Ordonnance</span>
                @else
                    <span class="bg-gray-50 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase"><i class="fas fa-file mr-1"></i>Autre</span>
                @endif
            </td>
            <td class="p-5"><span class="text-xs font-bold text-gray-600">{{ $doc->created_at->format('d/m/Y H:i') }}</span></td>
            <td class="p-5">
                <div class="flex justify-center gap-2">
                    <a href="{{ route('patient.documents.voir', $doc->id) }}" target="_blank"
                        class="w-9 h-9 flex items-center justify-center bg-cyan-50 text-cyan-500 rounded-xl hover:bg-cyan-500 hover:text-white transition-all" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('patient.documents.telecharger', $doc->id) }}"
                        class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all" title="Telecharger">
                        <i class="fas fa-download"></i>
                    </a>
                    <form action="{{ route('patient.documents.supprimer', $doc->id) }}" method="POST" onsubmit="return confirm('Supprimer ce document ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="p-16 text-center">
                <i class="fas fa-folder-open text-4xl text-gray-200 mb-3"></i>
                <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun document dans ce dossier</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
