<x-app-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>
    <x-slot name="metaDescription">{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 160) }}</x-slot>

    <article class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-ink dark:text-gray-100 mb-4">{{ $post->title }}</h1>
            <div class="flex flex-wrap items-center gap-4 text-sm text-steel dark:text-gray-400">
                <span>{{ $post->user->name }}</span>
                <span>{{ $post->published_at->format('Y-m-d') }}</span>
                @if($post->category)
                    <a href="{{ route('categories.show', $post->category) }}" class="px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-xs font-medium">
                        {{ $post->category->name }}
                    </a>
                @endif
                <span>{{ $post->views }} 次阅读</span>
            </div>
            @if($post->tags->isNotEmpty())
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('tags.show', $tag) }}" class="text-xs px-2.5 py-0.5 bg-surface dark:bg-gray-700 rounded-full text-slate dark:text-gray-400 hover:text-primary transition">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </header>

        <!-- Cover Image -->
        @if($post->cover)
            <img src="{{ asset('storage/' . $post->cover) }}" alt="{{ $post->title }}" class="w-full rounded-card mb-8">
        @endif

        <!-- Content -->
        <div class="prose prose-lg dark:prose-invert max-w-none mb-12">
            {!! $htmlContent !!}
        </div>

        <!-- Comments Section -->
        <section class="border-t border-hairline dark:border-gray-700 pt-8" id="comments">
            <h2 class="text-xl font-semibold text-ink dark:text-gray-100 mb-6">评论 ({{ $post->comments->count() }})</h2>

            <!-- Comment Form -->
            @auth
                <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-8">
                    @csrf
                    <textarea name="content" rows="3" placeholder="写下你的评论..."
                        class="w-full rounded-btn border-hairline dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-primary focus:ring-primary text-sm"></textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">
                        发表评论
                    </button>
                </form>
            @else
                <p class="mb-8 text-sm text-steel dark:text-gray-400">
                    <a href="{{ route('login') }}" class="text-primary hover:underline">登录</a> 后才能发表评论
                </p>
            @endauth

            <!-- Comments List -->
            <div class="space-y-6">
                @forelse($post->comments as $comment)
                    <div class="bg-white dark:bg-gray-800 rounded-card border border-hairline dark:border-gray-700 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-medium text-sm text-ink dark:text-gray-200">{{ $comment->user->name }}</span>
                            <span class="text-xs text-steel dark:text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-charcoal dark:text-gray-300">{{ $comment->content }}</p>

                        <!-- Replies -->
                        @if($comment->replies->isNotEmpty())
                            <div class="mt-4 ml-6 space-y-3">
                                @foreach($comment->replies as $reply)
                                    <div class="bg-surface-soft dark:bg-gray-700 rounded-btn p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-medium text-xs text-ink dark:text-gray-200">{{ $reply->user->name }}</span>
                                            <span class="text-xs text-steel dark:text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-charcoal dark:text-gray-300">{{ $reply->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-steel dark:text-gray-400 py-4">暂无评论，来发表第一条评论吧</p>
                @endforelse
            </div>
        </section>
    </article>
</x-app-layout>
