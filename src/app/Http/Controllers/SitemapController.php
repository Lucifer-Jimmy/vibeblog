<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()->orderByDesc('published_at')->get();
        $categories = Category::all();
        $tags = Tag::all();

        $content = view('sitemap', compact('posts', 'categories', 'tags'))->render();

        return response($content, 200)->header('Content-Type', 'application/xml');
    }
}
