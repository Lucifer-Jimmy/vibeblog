<x-app-layout>
    <x-slot name="title">403 - 无权访问</x-slot>

    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-6xl font-bold text-primary mb-4">403</h1>
        <p class="text-xl text-charcoal mb-8">抱歉，您没有权限访问此页面</p>
        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-primary text-white font-medium rounded-btn hover:bg-primary-pressed transition">
            返回首页
        </a>
    </div>
</x-app-layout>
