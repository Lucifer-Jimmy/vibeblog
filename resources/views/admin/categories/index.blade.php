<x-admin-layout>
    <x-slot name="title">分类管理</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Create Form -->
        <div class="bg-white rounded-card border border-hairline p-5">
            <h3 class="font-semibold text-ink mb-4">新建分类</h3>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <input type="text" name="name" placeholder="分类名称" required
                        class="w-full rounded-btn border-hairline text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <input type="text" name="description" placeholder="描述 (可选)"
                        class="w-full rounded-btn border-hairline text-sm focus:border-primary focus:ring-primary">
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">
                    创建
                </button>
            </form>
        </div>

        <!-- Categories List -->
        <div class="lg:col-span-2 bg-white rounded-card border border-hairline overflow-hidden">
            <div class="px-5 py-4 border-b border-hairline">
                <h3 class="font-semibold text-ink">所有分类</h3>
            </div>
            <div class="divide-y divide-hairline">
                @foreach($categories as $category)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <span class="text-sm font-medium text-ink">{{ $category->name }}</span>
                            <span class="text-xs text-steel ml-2">({{ $category->posts_count }} 篇文章)</span>
                            @if($category->description)
                                <p class="text-xs text-slate mt-0.5">{{ $category->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('确定删除？')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">删除</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
