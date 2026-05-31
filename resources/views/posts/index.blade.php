<x-app-layout>
    <x-slot name="title">首页</x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
        <!-- Hero Section -->
        <div class="bg-navy dark:bg-gray-800 rounded-card p-8 sm:p-12 mb-8 text-white border dark:border-gray-700">
            <h1 class="text-3xl sm:text-4xl font-bold mb-3">欢迎来到 {{ config('app.name') }}</h1>
            <p class="text-stone dark:text-gray-400 text-lg h-7"
               x-data="typewriter()"
               x-init="start()"
            ><span x-text="display"></span><span class="animate-pulse">|</span></p>
        </div>

        <!-- Posts List (single column) -->
        <div class="space-y-6">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="text-center py-12">
                    <p class="text-steel dark:text-gray-400 text-lg">暂无文章</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
