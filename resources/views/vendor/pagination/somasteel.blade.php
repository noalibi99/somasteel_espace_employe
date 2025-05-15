@if ($paginator->hasPages())
    <div class="flex flex-col items-center justify-center">
        <span class="text-xs text-gray-500 my-4">
            Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}
            • Affichage de {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }} sur {{ $paginator->total() }} résultats
        </span>
    </div>
    <nav class="mt-2 mb-4 flex justify-center" aria-label="Pagination">
        <ul class="inline-flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-400 cursor-not-allowed">
                        <i class="fa fa-chevron-left"></i>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-somasteel-orange/10 hover:text-somasteel-orange transition">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @php
                $start = max($paginator->currentPage() - 2, 1);
                $end = min($paginator->currentPage() + 2, $paginator->lastPage());
            @endphp
            @if($start > 1)
                <li>
                    <a href="{{ $paginator->url(1) }}" class="px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-somasteel-orange/10 hover:text-somasteel-orange transition">1</a>
                </li>
                @if($start > 2)
                    <li><span class="px-2">...</span></li>
                @endif
            @endif
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $paginator->currentPage())
                    <li>
                        <span class="px-3 py-1 rounded-full bg-somasteel-orange text-white font-semibold shadow">{{ $page }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->url($page) }}" class="px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-somasteel-orange/10 hover:text-somasteel-orange transition">{{ $page }}</a>
                    </li>
                @endif
            @endfor
            @if($end < $paginator->lastPage())
                @if($end < $paginator->lastPage() - 1)
                    <li><span class="px-2">...</span></li>
                @endif
                <li>
                    <a href="{{ $paginator->url($paginator->lastPage()) }}" class="px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-somasteel-orange/10 hover:text-somasteel-orange transition">{{ $paginator->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-500 hover:bg-somasteel-orange/10 hover:text-somasteel-orange transition">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-1 rounded-full bg-gray-200 text-gray-400 cursor-not-allowed">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
