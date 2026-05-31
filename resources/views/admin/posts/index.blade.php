<x-admin-layout>
    <x-slot name="title">文章管理</x-slot>

    <div class="bg-white rounded-card border border-hairline overflow-hidden">
        <div class="px-5 py-4 border-b border-hairline flex items-center justify-between">
            <h2 class="font-semibold text-ink">所有文章</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.posts.index') }}" class="text-xs px-3 py-1 rounded-full {{ !request('status') ? 'bg-primary text-white' : 'bg-surface text-charcoal' }}">全部</a>
                <a href="{{ route('admin.posts.index', ['status' => 'published']) }}" class="text-xs px-3 py-1 rounded-full {{ request('status') === 'published' ? 'bg-primary text-white' : 'bg-surface text-charcoal' }}">已发布</a>
                <a href="{{ route('admin.posts.index', ['status' => 'draft']) }}" class="text-xs px-3 py-1 rounded-full {{ request('status') === 'draft' ? 'bg-primary text-white' : 'bg-surface text-charcoal' }}">草稿</a>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-surface-soft">
                <tr>
                    <th class="text-left px-5 py-3 font-medium text-steel">标题</th>
                    <th class="text-left px-5 py-3 font-medium text-steel">作者</th>
                    <th class="text-left px-5 py-3 font-medium text-steel">分类</th>
                    <th class="text-left px-5 py-3 font-medium text-steel">状态</th>
                    <th class="text-left px-5 py-3 font-medium text-steel">日期</th>
                    <th class="text-right px-5 py-3 font-medium text-steel">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline">
                @foreach($posts as $post)
                    <tr>
                        <td class="px-5 py-3 text-ink">{{ Str::limit($post->title, 40) }}</td>
                        <td class="px-5 py-3 text-slate">{{ $post->user->name }}</td>
                        <td class="px-5 py-3 text-slate">{{ $post->category?->name ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $post->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $post->status === 'published' ? '已发布' : '草稿' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate">{{ $post->created_at->format('m-d') }}</td>
                        <td class="px-5 py-3 text-right">
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('确定永久删除？')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">删除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-hairline">
            {{ $posts->links() }}
        </div>
    </div>
</x-admin-layout>
