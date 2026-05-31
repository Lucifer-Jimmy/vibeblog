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

        $post = Post::create($data);

        if (!empty($data['tags'])) {
            $post->tags()->sync($data['tags']);
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

        // 如果从草稿变为发布，设置发布时间
        if ($data['status'] === 'published' && $post->status === 'draft') {
            $data['published_at'] = now();
        }

        $post->update($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

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
