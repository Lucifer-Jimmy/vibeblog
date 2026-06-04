<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->with(['user', 'category', 'tags'])
            ->orderByDesc('published_at')
            ->paginate(5);

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published' && $post->user_id !== auth()->id()) {
            abort(404);
        }

        $post->load(['user', 'category', 'tags', 'comments' => function ($query) {
            $query->visible()->whereNull('parent_id')->with(['user', 'replies.user'])->latest();
        }]);

        $post->increment('views');

        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $converter = new MarkdownConverter($environment);
        $htmlContent = $converter->convert($post->content)->getContent();

        return view('posts.show', compact('post', 'htmlContent'));
    }
}