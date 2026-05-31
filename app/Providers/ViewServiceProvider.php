<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $view->with('navCategories', Category::orderBy('name')->get());
        });
    }
}
