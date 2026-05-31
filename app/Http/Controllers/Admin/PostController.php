<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['user', 'category'])
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function destroy(Post $post)
    {
        $post->forceDelete();

        return back()->with('success', '文章已永久删除');
    }
}
