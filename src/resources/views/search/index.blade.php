<x-app-layout>
    <x-slot name="title">搜索{{ $query ? '：' . $query : '' }}</x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
        <!-- Search Form -->
        <form action="{{ route('search') }}" method="GET" class="mb-8">
            <div class="flex gap-2">
                <input type="text" name="q" value="{{ $query }}" placeholder="搜索文章..."
                    class="flex-1 rounded-btn border-hairline dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-primary focus:ring-primary">
                <button type="submit" class="px-6 py-2 bg-primary text-white font-medium rounded-btn hover:bg-primary-pressed transition">
                    搜索
                </button>
            </div>
        </form>

        @if($query)
            <h1 class="text-xl font-semibold text-ink dark:text-gray-100 mb-6">
                "{{ $query }}" 的搜索结果
                <span class="text-steel dark:text-gray-400 text-base font-normal">({{ $posts instanceof \Illuminate\Pagination\LengthAwarePaginator ? $posts->total() : $posts->count() }} 篇)</span>
            </h1>

            <div class="space-y-6">
                @forelse($posts as $post)
                    <x-post-card :post="$post" />
                @empty
                    <div class="text-center py-12">
                        <p class="text-steel dark:text-gray-400">没有找到相关文章，换个关键词试试？</p>
                    </div>
                @endforelse
            </div>

            @if($posts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <p class="text-steel dark:text-gray-400">输入关键词开始搜索</p>
            </div>
        @endif
    </div>
</x-app-layout>
