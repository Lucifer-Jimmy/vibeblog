<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-hairline dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-xl font-bold text-primary">
                    {{ config('app.name') }}
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ml-10 space-x-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-primary' : 'text-charcoal dark:text-gray-300 hover:text-primary' }} transition">
                        首页
                    </a>
                    <a href="{{ route('archives') }}" class="text-sm font-medium {{ request()->routeIs('archives') ? 'text-primary' : 'text-charcoal dark:text-gray-300 hover:text-primary' }} transition">
                        归档
                    </a>
                    <a href="{{ route('categories.index') }}" class="text-sm font-medium {{ request()->routeIs('categories.index') ? 'text-primary' : 'text-charcoal dark:text-gray-300 hover:text-primary' }} transition">
                        分类
                    </a>
                    <a href="{{ route('links') }}" class="text-sm font-medium {{ request()->routeIs('links') ? 'text-primary' : 'text-charcoal dark:text-gray-300 hover:text-primary' }} transition">
                        友链
                    </a>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center space-x-4">
                <!-- Search -->
                <form action="{{ route('search') }}" method="GET" class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="搜索..."
                        class="w-40 lg:w-56 pl-8 pr-3 py-1.5 text-sm rounded-full border-hairline dark:border-gray-600 bg-surface-soft dark:bg-gray-700 dark:text-gray-200 focus:border-primary focus:ring-primary focus:bg-white dark:focus:bg-gray-600 transition">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-steel dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </form>

                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-btn text-steel dark:text-gray-400 hover:text-charcoal dark:hover:text-gray-200 hover:bg-surface dark:hover:bg-gray-700 transition" title="切换主题">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                @auth
                    <a href="{{ route('my.posts.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">
                        写文章
                    </a>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-charcoal dark:text-gray-300 hover:text-primary transition">
                                {{ Auth::user()->name }}
                                <svg class="ml-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('my.posts.index')">我的文章</x-dropdown-link>
                            @if(Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('admin.dashboard')">后台管理</x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('profile.edit')">个人设置</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    退出登录
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-charcoal dark:text-gray-300 hover:text-primary transition">登录</a>
                    @if(setting('registration_enabled', 'true') === 'true')
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">注册</a>
                    @endif
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <!-- Mobile Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-md text-steel dark:text-gray-400 hover:text-charcoal dark:hover:text-gray-200 transition mr-1">
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
                <button @click="open = ! open" class="p-2 rounded-md text-steel dark:text-gray-400 hover:text-charcoal dark:hover:text-gray-200 hover:bg-surface dark:hover:bg-gray-700 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('home') }}" class="block py-2 text-sm font-medium text-charcoal dark:text-gray-300">首页</a>
            <a href="{{ route('archives') }}" class="block py-2 text-sm font-medium text-charcoal dark:text-gray-300">归档</a>
            <a href="{{ route('categories.index') }}" class="block py-2 text-sm font-medium text-charcoal dark:text-gray-300">分类</a>
            <a href="{{ route('links') }}" class="block py-2 text-sm font-medium text-charcoal dark:text-gray-300">友链</a>
        </div>
        @auth
            <div class="pt-4 pb-3 border-t border-hairline dark:border-gray-700 px-4">
                <div class="font-medium text-sm text-charcoal dark:text-gray-300">{{ Auth::user()->name }}</div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('my.posts.index') }}" class="block py-2 text-sm text-slate dark:text-gray-400">我的文章</a>
                    <a href="{{ route('my.posts.create') }}" class="block py-2 text-sm text-slate dark:text-gray-400">写文章</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2 text-sm text-slate dark:text-gray-400">退出登录</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
