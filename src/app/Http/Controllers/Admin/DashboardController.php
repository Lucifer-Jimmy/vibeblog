<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'posts' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'comments' => Comment::count(),
            'users' => User::count(),
        ];

        $recentPosts = Post::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentPosts'));
    }
}
