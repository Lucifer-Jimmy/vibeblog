<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $posts = collect();

        if (strlen($query) >= 2) {
            $posts = Post::published()
                ->with(['user', 'category', 'tags'])
                ->whereRaw('MATCH(title, content) AGAINST(? IN BOOLEAN MODE)', [$query . '*'])
                ->orderByDesc('published_at')
                ->paginate(10)
                ->appends(['q' => $query]);
        }

        return view('search.index', compact('posts', 'query'));
    }
}
