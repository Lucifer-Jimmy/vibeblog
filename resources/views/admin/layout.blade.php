<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>后台管理 - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-navy text-white flex-shrink-0">
            <div class="p-6">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">{{ config('app.name') }}</a>
                <p class="text-xs text-stone mt-1">后台管理</p>
            </div>
            <nav class="px-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-btn text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-stone hover:text-white hover:bg-white/5' }} transition">
                    仪表盘
                </a>
                <a href="{{ route('admin.posts.index') }}" class="flex items-center px-3 py-2 rounded-btn text-sm {{ request()->routeIs('admin.posts.*') ? 'bg-white/10 text-white' : 'text-stone hover:text-white hover:bg-white/5' }} transition">
                    文章管理
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2 rounded-btn text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white' : 'text-stone hover:text-white hover:bg-white/5' }} transition">
                    分类管理
                </a>
                <a href="{{ route('admin.comments.index') }}" class="flex items-center px-3 py-2 rounded-btn text-sm {{ request()->routeIs('admin.comments.*') ? 'bg-white/10 text-white' : 'text-stone hover:text-white hover:bg-white/5' }} transition">
                    评论审核
                </a>
                <hr class="border-white/10 my-4">
                <a href="{{ route('home') }}" class="flex items-center px-3 py-2 rounded-btn text-sm text-stone hover:text-white hover:bg-white/5 transition">
                    ← 返回前台
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b border-hairline px-6 py-4 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-ink">{{ $title ?? '后台管理' }}</h1>
                <span class="text-sm text-steel">{{ Auth::user()->name }}</span>
            </header>
            <main class="flex-1 p-6 bg-surface-soft">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-btn text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-btn text-red-700 text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
