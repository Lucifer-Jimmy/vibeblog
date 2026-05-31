<x-app-layout>
    <x-slot name="title">编辑文章</x-slot>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-ink mb-6">编辑文章</h1>

        <form action="{{ route('my.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-charcoal mb-1">标题</label>
                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}"
                    class="w-full rounded-btn border-hairline focus:border-primary focus:ring-primary"
                    placeholder="输入文章标题" required>
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-charcoal mb-1">分类</label>
                    <select name="category_id" id="category_id" class="w-full rounded-btn border-hairline focus:border-primary focus:ring-primary">
                        <option value="">选择分类</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-charcoal mb-1">状态</label>
                    <select name="status" id="status" class="w-full rounded-btn border-hairline focus:border-primary focus:ring-primary">
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>草稿</option>
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>发布</option>
                    </select>
                </div>
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-sm font-medium text-charcoal mb-2">标签</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                class="rounded border-hairline text-primary focus:ring-primary">
                            <span class="ml-1.5 text-sm text-charcoal">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Cover Image -->
            <div>
                <label for="cover" class="block text-sm font-medium text-charcoal mb-1">封面图 (可选)</label>
                @if($post->cover)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $post->cover) }}" alt="当前封面" class="h-32 rounded-btn object-cover">
                    </div>
                @endif
                <input type="file" name="cover" id="cover" accept="image/jpeg,image/png,image/webp"
                    class="w-full text-sm text-slate file:mr-4 file:py-2 file:px-4 file:rounded-btn file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                @error('cover')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-charcoal mb-1">摘要 (可选)</label>
                <textarea name="excerpt" id="excerpt" rows="2"
                    class="w-full rounded-btn border-hairline focus:border-primary focus:ring-primary text-sm"
                    placeholder="文章摘要">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-charcoal mb-1">内容 (支持 Markdown)</label>
                <textarea name="content" id="content" rows="16"
                    class="w-full rounded-btn border-hairline focus:border-primary focus:ring-primary text-sm font-mono"
                    placeholder="使用 Markdown 格式书写..." required>{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white font-medium rounded-btn hover:bg-primary-pressed transition">
                    更新文章
                </button>
                <a href="{{ route('my.posts.index') }}" class="px-6 py-2.5 text-charcoal border border-hairline rounded-btn hover:bg-surface transition">
                    取消
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
