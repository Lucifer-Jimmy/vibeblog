<x-app-layout>
    <x-slot name="title">我的文章</x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-ink dark:text-gray-100">我的文章</h1>
            <a href="{{ route('my.posts.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">
                写文章
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-btn text-green-700 dark:text-green-400 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-card border border-hairline dark:border-gray-700 overflow-hidden">
            @forelse($posts as $post)
                <div class="flex items-center justify-between p-4 {{ !$loop->last ? 'border-b border-hairline dark:border-gray-700' : '' }}">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-ink dark:text-gray-200 truncate">
                            <a href="{{ $post->status === 'published' ? route('posts.show', $post) : '#' }}" class="hover:text-primary">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $post->status === 'published' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' }}">
                                {{ $post->status === 'published' ? '已发布' : '草稿' }}
                            </span>
                            @if($post->category)
                                <span class="text-xs text-steel dark:text-gray-500">{{ $post->category->name }}</span>
                            @endif
                            <span class="text-xs text-steel dark:text-gray-500">{{ $post->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <a href="{{ route('my.posts.edit', $post) }}" class="text-xs text-primary hover:underline">编辑</a>
                        <form action="{{ route('my.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('确定要删除这篇文章吗？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline">删除</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-steel dark:text-gray-400 mb-4">还没有写过文章</p>
                    <a href="{{ route('my.posts.create') }}" class="text-primary hover:underline">开始写第一篇</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>
