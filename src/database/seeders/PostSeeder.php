<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::factory(6)->create();
        $tags = Tag::factory(10)->create();

        Post::factory(35)
            ->recycle($users)
            ->recycle($categories)
            ->create()
            ->each(function (Post $post) use ($tags) {

                $post->tags()->attach(
                    $tags->random(rand(1, 4))->pluck('id')->toArray()
                );

                if ($post->status === 'published') {
                    $commentUsers = User::inRandomOrder()->limit(rand(1, 3))->get();
                    foreach ($commentUsers as $user) {
                        Comment::factory()->create([
                            'post_id' => $post->id,
                            'user_id' => $user->id,
                        ]);
                    }
                }
            });
    }
}
