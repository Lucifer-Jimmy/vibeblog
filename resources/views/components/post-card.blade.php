@props(['post'])

<article class="bg-white rounded-card border border-hairline overflow-hidden hover:shadow-md transition-shadow">
    @if($post->cover)
        <a href="{{ route('posts.show', $post) }}">
            <img src="{{ asset('storage/' . $post->cover) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
        </a>
    @endif
    <div class="p-6">
        <div class="flex items-center gap-2 mb-3">
            @if($post->category)
                <a href="{{ route('categories.show', $post->category) }}" class="inline-block px-2.5 py-0.5 text-xs font-medium bg-primary/10 text-primary rounded-full">
                    {{ $post->category->name }}
                </a>
            @endif
            <span class="text-xs text-steel">{{ $post->published_at->diffForHumans() }}</span>
        </div>

        <h2 class="text-lg font-semibold text-ink mb-2 line-clamp-2">
            <a href="{{ route('posts.show', $post) }}" class="hover:text-primary transition">
                {{ $post->title }}
            </a>
        </h2>

        @if($post->excerpt)
            <p class="text-sm text-slate line-clamp-2 mb-4">{{ $post->excerpt }}</p>
        @endif

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs text-steel">{{ $post->user->name }}</span>
            </div>
            <div class="flex items-center gap-1">
                @foreach($post->tags->take(3) as $tag)
                    <a href="{{ route('tags.show', $tag) }}" class="text-xs px-2 py-0.5 bg-surface rounded-full text-slate hover:text-primary transition">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</article>
