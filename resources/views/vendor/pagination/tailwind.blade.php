@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between mt-6">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                    Précédent
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 text-sm font-medium text-white bg-somasteel-orange hover:bg-somasteel-orange/90 rounded-md shadow">
                    Précédent
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="ml-3 px-4 py-2 text-sm font-medium text-white bg-somasteel-orange hover:bg-somasteel-orange/90 rounded-md shadow">
                    Suivant
                </a>
            @else
                <span class="ml-3 px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md cursor-not-allowed">
                    Suivant
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Page
                    <span class="font-medium">{{ $paginator->currentPage() }}</span>
                    sur
                    <span class="font-medium">{{ $paginator->lastPage() }}</span>
                    •
                    Affichage de
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    à
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    sur
                    <span class="font-medium">{{ $paginator->total() }}</span> résultats
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md" aria-label="Pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-l-md">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:text-somasteel-orange">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-somasteel-orange border border-somasteel-orange z-10">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:text-somasteel-orange">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:text-somasteel-orange">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span aria-disabled="true" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-400 bg-white border border-gray-300 rounded-r-md">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif