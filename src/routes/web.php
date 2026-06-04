<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\MyPostController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/archives', [ArchiveController::class, 'index'])->name('archives');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/links', [LinkController::class, 'index'])->name('links');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

Route::middleware('auth')->group(function () {

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/my/posts', [MyPostController::class, 'index'])->name('my.posts.index');
    Route::get('/my/posts/create', [MyPostController::class, 'create'])->name('my.posts.create');
    Route::post('/my/posts', [MyPostController::class, 'store'])->name('my.posts.store');
    Route::get('/my/posts/{post}/edit', [MyPostController::class, 'edit'])->name('my.posts.edit');
    Route::put('/my/posts/{post}', [MyPostController::class, 'update'])->name('my.posts.update');
    Route::delete('/my/posts/{post}', [MyPostController::class, 'destroy'])->name('my.posts.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/posts', [Admin\PostController::class, 'index'])->name('posts.index');
    Route::delete('/posts/{post}', [Admin\PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('/comments', [Admin\CommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/toggle', [Admin\CommentController::class, 'toggleVisibility'])->name('comments.toggle');
    Route::delete('/comments/{comment}', [Admin\CommentController::class, 'destroy'])->name('comments.destroy');
    Route::get('/links', [Admin\LinkController::class, 'index'])->name('links.index');
    Route::post('/links', [Admin\LinkController::class, 'store'])->name('links.store');
    Route::put('/links/{link}', [Admin\LinkController::class, 'update'])->name('links.update');
    Route::delete('/links/{link}', [Admin\LinkController::class, 'destroy'])->name('links.destroy');
    Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});

Route::get('/dashboard', function () {
    return redirect()->route('my.posts.index');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
