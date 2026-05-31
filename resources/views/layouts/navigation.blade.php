<nav x-data="{ open: false }" class="bg-white border-b border-hairline">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-xl font-bold text-primary">
                    {{ config('app.name') }}
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ml-10 space-x-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-primary' : 'text-charcoal hover:text-primary' }} transition">
                        首页
                    </a>
                    @foreach($navCategories ?? [] as $cat)
                        <a href="{{ route('categories.show', $cat) }}" class="text-sm font-medium {{ request()->is('categories/' . $cat->slug) ? 'text-primary' : 'text-charcoal hover:text-primary' }} transition">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center space-x-4">
                @auth
                    <a href="{{ route('my.posts.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">
                        写文章
                    </a>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-charcoal hover:text-primary transition">
                                {{ Auth::user()->name }}
                                <svg class="ml-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('my.posts.index')">我的文章</x-dropdown-link>
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
                    <a href="{{ route('login') }}" class="text-sm font-medium text-charcoal hover:text-primary transition">登录</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-btn hover:bg-primary-pressed transition">注册</a>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-steel hover:text-charcoal hover:bg-surface transition">
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
            <a href="{{ route('home') }}" class="block py-2 text-sm font-medium text-charcoal">首页</a>
            @foreach($navCategories ?? [] as $cat)
                <a href="{{ route('categories.show', $cat) }}" class="block py-2 text-sm font-medium text-charcoal">{{ $cat->name }}</a>
            @endforeach
        </div>
        @auth
            <div class="pt-4 pb-3 border-t border-hairline px-4">
                <div class="font-medium text-sm text-charcoal">{{ Auth::user()->name }}</div>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('my.posts.index') }}" class="block py-2 text-sm text-slate">我的文章</a>
                    <a href="{{ route('my.posts.create') }}" class="block py-2 text-sm text-slate">写文章</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2 text-sm text-slate">退出登录</button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
