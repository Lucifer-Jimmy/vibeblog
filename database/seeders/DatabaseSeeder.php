<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 创建角色
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'author']);
        Role::create(['name' => 'user']);

        // 创建管理员
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@myblog.test',
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        // 创建普通用户
        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@myblog.test',
            'role' => 'user',
        ]);
        $user->assignRole('user');

        // 创建额外测试用户
        User::factory(3)->create()->each(fn ($u) => $u->assignRole('user'));

        $this->call([
            PostSeeder::class,
        ]);
    }
}
