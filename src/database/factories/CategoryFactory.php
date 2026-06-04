<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            '技术', '生活', '读书', '旅行', '美食',
            '摄影', '设计', '前端', '后端', '运维',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) ?: Str::random(8),
            'description' => fake()->sentence(),
        ];
    }
}
