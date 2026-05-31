<x-admin-layout>
    <x-slot name="title">评论审核</x-slot>

    <div class="bg-white rounded-card border border-hairline overflow-hidden">
        <div class="px-5 py-4 border-b border-hairline">
            <h2 class="font-semibold text-ink">所有评论</h2>
        </div>
        <div class="divide-y divide-hairline">
            @forelse($comments as $comment)
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-sm font-medium text-ink">{{ $comment->user->name }}</span>
                                <span class="text-xs text-steel">评论于</span>
                                <a href="{{ route('posts.show', $comment->post) }}" class="text-xs text-primary hover:underline truncate">
                                    {{ Str::limit($comment->post->title, 30) }}
                                </a>
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $comment->status === 'visible' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $comment->status === 'visible' ? '可见' : '隐藏' }}
                                </span>
                            </div>
                            <p class="text-sm text-charcoal">{{ $comment->content }}</p>
                            <p class="text-xs text-steel mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            <form action="{{ route('admin.comments.toggle', $comment) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="text-xs {{ $comment->status === 'visible' ? 'text-yellow-600' : 'text-green-600' }} hover:underline">
                                    {{ $comment->status === 'visible' ? '隐藏' : '显示' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('确定删除？')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">删除</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-steel">暂无评论</div>
            @endforelse
        </div>
        <div class="px-5 py-4 border-t border-hairline">
            {{ $comments->links() }}
        </div>
    </div>
</x-admin-layout>
