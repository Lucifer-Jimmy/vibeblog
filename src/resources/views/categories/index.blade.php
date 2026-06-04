<x-app-layout>
    <x-slot name="title">分类</x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
        <h1 class="text-2xl font-bold text-ink dark:text-gray-100 mb-8">分类</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="block bg-white dark:bg-gray-800 rounded-card border border-hairline dark:border-gray-700 p-5 hover:shadow-md dark:hover:shadow-gray-900/30 transition-shadow">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-ink dark:text-gray-100">{{ $category->name }}</h2>
                        <span class="text-xs text-steel dark:text-gray-500 bg-surface dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ $category->posts_count }} 篇</span>
                    </div>
                    @if($category->description)
                        <p class="text-sm text-slate dark:text-gray-400 mt-2">{{ $category->description }}</p>
                    @endif
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-steel dark:text-gray-400">暂无分类</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
