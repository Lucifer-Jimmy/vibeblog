<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MyPostController extends Controller
{
    public function index(Request $request)
    {
        $posts = $request->user()->posts()
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('my.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('my.posts.create', compact('categories', 'tags'));
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        unset($data['tags']);
        $post = Post::create($data);

        if (!empty($request->validated('tags'))) {
            $post->tags()->sync($request->validated('tags'));
        }

        return redirect()->route('my.posts.index')->with('success', '文章创建成功');
    }

    public function edit(Post $post)
    {
        $this->authorizePost($post);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $post->load('tags');

        return view('my.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorizePost($post);

        $data = $request->validated();

        if ($data['status'] === 'published' && $post->status === 'draft') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('cover')) {
            if ($post->cover) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($post->cover);
            }
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        $tags = $data['tags'] ?? [];
        unset($data['tags']);
        $post->update($data);
        $post->tags()->sync($tags);

        return redirect()->route('my.posts.index')->with('success', '文章更新成功');
    }

    public function destroy(Post $post)
    {
        $this->authorizePost($post);

        $post->delete();

        return redirect()->route('my.posts.index')->with('success', '文章已删除');
    }

    private function authorizePost(Post $post): void
    {
        if ($post->user_id !== auth()->id()) {
            abort(403, '无权操作此文章');
        }
    }
}