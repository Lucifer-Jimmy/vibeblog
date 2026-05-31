<x-app-layout>
    <x-slot name="title">首页</x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-navy rounded-card p-8 sm:p-12 mb-8 text-white">
            <h1 class="text-3xl sm:text-4xl font-bold mb-3">欢迎来到 {{ config('app.name') }}</h1>
            <p class="text-stone text-lg">分享技术、记录生活、探索世界</p>
        </div>

        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-steel text-lg">暂无文章</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
