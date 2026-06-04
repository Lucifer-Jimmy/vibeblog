<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_homepage(): void
    {
        Post::factory(3)->published()->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('posts');
    }

    public function test_guest_can_view_published_post(): void
    {
        $post = Post::factory()->published()->create();

        $response = $this->get("/posts/{$post->slug}");

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_guest_cannot_view_draft_post(): void
    {
        $post = Post::factory()->draft()->create();

        $response = $this->get("/posts/{$post->slug}");

        $response->assertStatus(404);
    }

    public function test_guest_cannot_access_create_post_page(): void
    {
        $response = $this->get('/my/posts/create');

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_post(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post('/my/posts', [
            'title' => '测试文章标题',
            'content' => '这是测试文章内容',
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('my.posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => '测试文章标题',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_only_edit_own_post(): void
    {
        $author = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        // 作者可以编辑
        $this->actingAs($author)->get("/my/posts/{$post->id}/edit")->assertStatus(200);

        // 其他人不能编辑
        $this->actingAs($other)->get("/my/posts/{$post->id}/edit")->assertStatus(403);
    }

    public function test_guest_cannot_comment(): void
    {
        $post = Post::factory()->published()->create();

        $response = $this->post("/posts/{$post->id}/comments", [
            'content' => '测试评论',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_user_can_comment_on_published_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($user)->post("/posts/{$post->id}/comments", [
            'content' => '这是一条评论',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => '这是一条评论',
        ]);
    }

    public function test_category_page_shows_posts(): void
    {
        $category = Category::factory()->create();
        Post::factory(2)->published()->create(['category_id' => $category->id]);

        $response = $this->get("/categories/{$category->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('posts');
    }

    public function test_tag_page_shows_posts(): void
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->published()->create();
        $post->tags()->attach($tag);

        $response = $this->get("/tags/{$tag->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('posts');
    }
}
