<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MyPostController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// 前台路由
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

// 需要登录的路由
Route::middleware('auth')->group(function () {
    // 评论
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    // 我的文章
    Route::get('/my/posts', [MyPostController::class, 'index'])->name('my.posts.index');
    Route::get('/my/posts/create', [MyPostController::class, 'create'])->name('my.posts.create');
    Route::post('/my/posts', [MyPostController::class, 'store'])->name('my.posts.store');
    Route::get('/my/posts/{post}/edit', [MyPostController::class, 'edit'])->name('my.posts.edit');
    Route::put('/my/posts/{post}', [MyPostController::class, 'update'])->name('my.posts.update');
    Route::delete('/my/posts/{post}', [MyPostController::class, 'destroy'])->name('my.posts.destroy');

    // 个人设置
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    return redirect()->route('my.posts.index');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
