<x-admin-layout>
    <x-slot name="title">友链管理</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Create Form -->
        <div class="bg-white rounded-card border border-hairline p-5">
            <h3 class="font-semibold text-ink mb-4">添加友链</h3>
            <form action="{{ route('admin.links.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <input type="text" name="name" placeholder="名称 *" required
                        class="w-full rounded-btn border-hairline text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <input type="url" name="url" placeholder="网址 * (https://...)" required
                        class="w-full rounded-btn border-hairline text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <input type="text" name="description" placeholder="简介 (可选)"
                        class="w-full rounded-btn border-hairline text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <input type="url" name="avatar" placeholder="头像 URL (可选)"
                        class="w-full rounded-btn border-hairline text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <select name="status" class="w-full rounded-btn border-hairline text-sm focus:border-primary focus:ring-primary">
                        <option value="visible">显示</option>
                        <option value="hidden">隐藏</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">
                    添加
                </button>
            </form>
        </div>

        <!-- Links List -->
        <div class="lg:col-span-2 bg-white rounded-card border border-hairline overflow-hidden">
            <div class="px-5 py-4 border-b border-hairline">
                <h3 class="font-semibold text-ink">所有友链</h3>
            </div>
            <div class="divide-y divide-hairline">
                @forelse($links as $link)
                    <div class="px-5 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($link->avatar)
                                <img src="{{ $link->avatar }}" alt="{{ $link->name }}" class="w-8 h-8 rounded-full object-cover shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="text-primary font-semibold text-xs">{{ mb_substr($link->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-ink truncate">{{ $link->name }}</p>
                                <p class="text-xs text-steel truncate">{{ $link->url }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 ml-4 shrink-0">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $link->status === 'visible' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $link->status === 'visible' ? '显示' : '隐藏' }}
                            </span>
                            <form action="{{ route('admin.links.destroy', $link) }}" method="POST" onsubmit="return confirm('确定删除？')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">删除</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-steel">暂无友链</div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
