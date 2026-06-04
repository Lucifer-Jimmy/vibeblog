<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Laravel', 'PHP', 'Vue.js', 'React', 'MySQL',
            'Docker', 'Linux', 'Git', 'API', 'TDD',
            'CSS', 'JavaScript', 'TypeScript', 'Redis', 'Nginx',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
