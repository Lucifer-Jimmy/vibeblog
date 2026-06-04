<x-app-layout>
    <x-slot name="title">404 - 页面未找到</x-slot>

    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-6xl font-bold text-primary mb-4">404</h1>
        <p class="text-xl text-charcoal mb-8">抱歉，您访问的页面不存在</p>
        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-primary text-white font-medium rounded-btn hover:bg-primary-pressed transition">
            返回首页
        </a>
    </div>
</x-app-layout>
