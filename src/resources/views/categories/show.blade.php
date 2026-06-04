<x-app-layout>
    <x-slot name="title">分类：{{ $category->name }}</x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-ink">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-2 text-steel">{{ $category->description }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <x-post-card :post="$post" />
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-steel">该分类下暂无文章</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
