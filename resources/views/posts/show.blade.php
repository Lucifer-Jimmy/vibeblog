<x-app-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>

    <article class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-ink mb-4">{{ $post->title }}</h1>
            <div class="flex flex-wrap items-center gap-4 text-sm text-steel">
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
                        <a href="{{ route('tags.show', $tag) }}" class="text-xs px-2.5 py-0.5 bg-surface rounded-full text-slate hover:text-primary transition">
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
        <div class="prose prose-lg max-w-none mb-12">
            {!! $htmlContent !!}
        </div>

        <!-- Comments Section -->
        <section class="border-t border-hairline pt-8" id="comments">
            <h2 class="text-xl font-semibold text-ink mb-6">评论 ({{ $post->comments->count() }})</h2>

            <!-- Comment Form -->
            @auth
                <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-8">
                    @csrf
                    <textarea name="content" rows="3" placeholder="写下你的评论..."
                        class="w-full rounded-btn border-hairline focus:border-primary focus:ring-primary text-sm"></textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="mt-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">
                        发表评论
                    </button>
                </form>
            @else
                <p class="mb-8 text-sm text-steel">
                    <a href="{{ route('login') }}" class="text-primary hover:underline">登录</a> 后才能发表评论
                </p>
            @endauth

            <!-- Comments List -->
            <div class="space-y-6">
                @forelse($post->comments as $comment)
                    <div class="bg-white rounded-card border border-hairline p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-medium text-sm text-ink">{{ $comment->user->name }}</span>
                            <span class="text-xs text-steel">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-charcoal">{{ $comment->content }}</p>

                        <!-- Replies -->
                        @if($comment->replies->isNotEmpty())
                            <div class="mt-4 ml-6 space-y-3">
                                @foreach($comment->replies as $reply)
                                    <div class="bg-surface-soft rounded-btn p-3">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-medium text-xs text-ink">{{ $reply->user->name }}</span>
                                            <span class="text-xs text-steel">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-charcoal">{{ $reply->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-steel py-4">暂无评论，来发表第一条评论吧</p>
                @endforelse
            </div>
        </section>
    </article>
</x-app-layout>
