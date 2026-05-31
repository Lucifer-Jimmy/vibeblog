<x-admin-layout>
    <x-slot name="title">站点设置</x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <div class="bg-white rounded-card border border-hairline overflow-hidden">
                <div class="px-5 py-4 border-b border-hairline">
                    <h2 class="font-semibold text-ink">注册设置</h2>
                </div>
                <div class="p-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="registration_enabled" value="1"
                            {{ $settings['registration_enabled'] === 'true' ? 'checked' : '' }}
                            class="rounded border-hairline text-primary focus:ring-primary w-5 h-5">
                        <div>
                            <span class="text-sm font-medium text-ink">开放用户注册</span>
                            <p class="text-xs text-steel mt-0.5">关闭后，新用户将无法注册账号，导航栏的注册按钮也会隐藏</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white font-medium rounded-btn hover:bg-primary-pressed transition">
                    保存设置
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
