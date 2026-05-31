<x-app-layout>
    <x-slot name="title">友链</x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-ink dark:text-gray-100 mb-8">友链</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($links as $link)
                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                   class="block bg-white dark:bg-gray-800 rounded-card border border-hairline dark:border-gray-700 p-5 hover:shadow-md dark:hover:shadow-gray-900/30 transition-shadow">
                    <div class="flex items-center gap-3">
                        @if($link->avatar)
                            <img src="{{ $link->avatar }}" alt="{{ $link->name }}" class="w-10 h-10 rounded-full object-cover shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="text-primary font-semibold text-sm">{{ mb_substr($link->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-ink dark:text-gray-100 truncate">{{ $link->name }}</h3>
                            @if($link->description)
                                <p class="text-xs text-slate dark:text-gray-400 truncate mt-0.5">{{ $link->description }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 rounded-card border border-hairline dark:border-gray-700 p-8 text-center">
                    <p class="text-steel dark:text-gray-400 mb-2">暂无友链</p>
                    <p class="text-sm text-stone dark:text-gray-500">如果你想交换友链，欢迎联系我 ✉️</p>
                </div>
            @endforelse
        </div>

        @if($links->hasPages())
            <div class="mt-8">
                {{ $links->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
