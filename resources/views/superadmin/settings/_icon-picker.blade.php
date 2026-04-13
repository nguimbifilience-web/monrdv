{{--
    Icon picker reutilisable.
    Variables attendues :
      - $inputId  : id de l'input texte qui stocke la classe FA (ex: "es_icone")
      - $icons    : tableau de classes FontAwesome (ex: ['fa-stethoscope', 'fa-heart', ...])
--}}
<div class="grid grid-cols-6 sm:grid-cols-8 gap-2 p-3 bg-gray-50 rounded-xl border-2 border-gray-100 max-h-40 overflow-y-auto icon-picker" data-target="{{ $inputId }}">
    @foreach($icons as $ic)
        <button type="button" data-icon="{{ $ic }}"
                class="w-9 h-9 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-500 hover:text-white transition-all"
                title="{{ $ic }}">
            <i class="fas {{ $ic }} text-sm"></i>
        </button>
    @endforeach
</div>
