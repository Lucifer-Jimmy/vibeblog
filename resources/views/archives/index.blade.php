<x-app-layout>
    <x-slot name="title">归档</x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
        <h1 class="text-2xl font-bold text-ink dark:text-gray-100 mb-8">归档</h1>

        @forelse($grouped as $year => $yearPosts)
            <section class="mb-10">
                <h2 class="text-lg font-semibold text-primary mb-4">{{ $year }}</h2>
                <ul class="space-y-3 border-l-2 border-hairline dark:border-gray-700 pl-6">
                    @foreach($yearPosts as $post)
                        <li class="relative">
                            <span class="absolute -left-[1.625rem] top-1.5 w-2.5 h-2.5 bg-primary/30 rounded-full border-2 border-primary"></span>
                            <div class="flex items-baseline gap-3">
                                <span class="text-sm text-steel dark:text-gray-500 font-mono shrink-0">{{ $post->published_at->format('m-d') }}</span>
                                <a href="{{ route('posts.show', $post) }}" class="text-sm font-medium text-ink dark:text-gray-200 hover:text-primary transition truncate">
                                    {{ $post->title }}
                                </a>
                                <span class="text-xs text-stone dark:text-gray-500 shrink-0 hidden sm:inline">{{ $post->user->name }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @empty
            <div class="text-center py-12">
                <p class="text-steel dark:text-gray-400">暂无文章</p>
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
