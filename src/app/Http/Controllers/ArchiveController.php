<?php

namespace App\Http\Controllers;

use App\Models\Post;

class ArchiveController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with('user')
            ->orderByDesc('published_at')
            ->paginate(30);

        $grouped = $posts->getCollection()->groupBy(fn ($post) => $post->published_at->format('Y'));

        return view('archives.index', compact('posts', 'grouped'));
    }
}