<x-admin-layout>
    <x-slot name="title">仪表盘</x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-card border border-hairline p-5">
            <p class="text-sm text-steel">总文章数</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ $stats['posts'] }}</p>
        </div>
        <div class="bg-white rounded-card border border-hairline p-5">
            <p class="text-sm text-steel">已发布</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ $stats['published'] }}</p>
        </div>
        <div class="bg-white rounded-card border border-hairline p-5">
            <p class="text-sm text-steel">评论数</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ $stats['comments'] }}</p>
        </div>
        <div class="bg-white rounded-card border border-hairline p-5">
            <p class="text-sm text-steel">用户数</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ $stats['users'] }}</p>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="bg-white rounded-card border border-hairline">
        <div class="px-5 py-4 border-b border-hairline">
            <h2 class="font-semibold text-ink">最近文章</h2>
        </div>
        <div class="divide-y divide-hairline">
            @foreach($recentPosts as $post)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-ink">{{ $post->title }}</p>
                        <p class="text-xs text-steel">{{ $post->user->name }} · {{ $post->created_at->format('Y-m-d') }}</p>
                    </div>
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $post->status === 'published' ? '已发布' : '草稿' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
