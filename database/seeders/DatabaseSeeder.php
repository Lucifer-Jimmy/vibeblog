<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 创建管理员
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@myblog.test',
            'role' => 'admin',
        ]);

        // 创建普通用户
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@myblog.test',
            'role' => 'user',
        ]);

        // 创建额外测试用户
        User::factory(3)->create();

        $this->call([
            PostSeeder::class,
        ]);
    }
}
