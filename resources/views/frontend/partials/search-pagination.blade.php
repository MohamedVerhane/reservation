@if($hotels->hasPages())
    <div class="mt-12 flex justify-center">
        <nav class="flex items-center gap-2">

            {{-- Previous --}}
            @if($hotels->previousPageUrl())
                <a href="{{ $hotels->previousPageUrl() }}"
                   @click.prevent="goToPage('{{ $hotels->previousPageUrl() }}')"
                   class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200/80 bg-white/80 text-slate-600 backdrop-blur-sm transition-all hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 dark:border-slate-700/80 dark:bg-slate-800/80 dark:text-slate-400 dark:hover:border-amber-600 dark:hover:bg-amber-500/10 dark:hover:text-amber-300">
                    <i class="bi bi-chevron-left text-sm"></i>
                </a>
            @endif

            {{-- Pages --}}
            @foreach($hotels->links()->elements as $page => $url)
                @if(is_string($page))
                    <span class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-slate-200/80 bg-white/80 px-3 text-sm font-medium text-slate-400 dark:border-slate-700/80 dark:bg-slate-800/80 dark:text-slate-500">
                        {{ $page }}
                    </span>
                @else
                    @if($page == $hotels->currentPage())
                        <span class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-3 text-sm font-bold text-white shadow-md shadow-amber-500/25">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           @click.prevent="goToPage('{{ $url }}')"
                           class="flex h-10 min-w-[2.5rem] items-center justify-center rounded-xl border border-slate-200/80 bg-white/80 px-3 text-sm font-medium text-slate-600 backdrop-blur-sm transition-all hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 dark:border-slate-700/80 dark:bg-slate-800/80 dark:text-slate-400 dark:hover:border-amber-600 dark:hover:bg-amber-500/10 dark:hover:text-amber-300">
                            {{ $page }}
                        </a>
                    @endif
                @endif
            @endforeach

            {{-- Next --}}
            @if($hotels->nextPageUrl())
                <a href="{{ $hotels->nextPageUrl() }}"
                   @click.prevent="goToPage('{{ $hotels->nextPageUrl() }}')"
                   class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200/80 bg-white/80 text-slate-600 backdrop-blur-sm transition-all hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 dark:border-slate-700/80 dark:bg-slate-800/80 dark:text-slate-400 dark:hover:border-amber-600 dark:hover:bg-amber-500/10 dark:hover:text-amber-300">
                    <i class="bi bi-chevron-right text-sm"></i>
                </a>
            @endif

        </nav>
    </div>
@endif
