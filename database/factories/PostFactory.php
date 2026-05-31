<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(rand(4, 8));
        $isPublished = fake()->boolean(70);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'excerpt' => fake()->paragraph(1),
            'content' => $this->generateMarkdownContent(),
            'cover' => null,
            'status' => $isPublished ? 'published' : 'draft',
            'views' => $isPublished ? fake()->numberBetween(0, 500) : 0,
            'published_at' => $isPublished ? fake()->dateTimeBetween('-3 months', 'now') : null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    private function generateMarkdownContent(): string
    {
        $paragraphs = fake()->paragraphs(rand(3, 6));
        $content = "## " . fake()->sentence() . "\n\n";
        $content .= implode("\n\n", $paragraphs);
        $content .= "\n\n### " . fake()->sentence() . "\n\n";
        $content .= fake()->paragraph(3);
        $content .= "\n\n```php\n// 示例代码\n\$result = collect([1, 2, 3])->map(fn (\$i) => \$i * 2);\n```\n\n";
        $content .= fake()->paragraph(2);

        return $content;
    }
}
