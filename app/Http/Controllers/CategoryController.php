<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $posts = $category->posts()
            ->published()
            ->with(['user', 'tags'])
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('categories.show', compact('category', 'posts'));
    }
}
