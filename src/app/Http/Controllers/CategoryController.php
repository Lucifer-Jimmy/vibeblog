<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

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
