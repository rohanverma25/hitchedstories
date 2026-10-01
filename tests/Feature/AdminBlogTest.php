<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBlogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');

        $this->actingAs(Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]), 'admin');
    }

    private function payload(array $overrides = []): array
    {
        return [
            'title' => 'Our First Post',
            'slug' => '',
            'excerpt' => 'Short summary.',
            'content' => '<p>Hello world</p>',
            'meta_title' => 'First post meta',
            'meta_description' => 'Meta description.',
            'meta_keywords' => 'wedding, planning',
            'is_published' => '1',
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_blogs(): void
    {
        auth('admin')->logout();

        $this->get('/admin/blogs')->assertRedirect('/admin/login');
        $this->post('/admin/blogs', $this->payload())->assertRedirect('/admin/login');
        $this->assertDatabaseCount('blogs', 0);
    }

    public function test_blog_pages_are_displayed(): void
    {
        $this->post('/admin/blogs', $this->payload());
        $blog = Blog::first();

        $this->get('/admin/blogs')->assertOk()->assertSee('Our First Post');
        $this->get('/admin/blogs/create')->assertOk();
        $this->get("/admin/blogs/{$blog->id}/edit")->assertOk()->assertSee('First post meta');
    }

    public function test_blog_is_created_with_meta_and_a_slug_generated_from_the_title(): void
    {
        $this->post('/admin/blogs', $this->payload())->assertRedirect('/admin/blogs');

        $blog = Blog::first();
        $this->assertSame('our-first-post', $blog->slug);
        $this->assertSame('First post meta', $blog->meta_title);
        $this->assertSame('Meta description.', $blog->meta_description);
        $this->assertSame('wedding, planning', $blog->meta_keywords);
        $this->assertTrue($blog->is_published);
        $this->assertNotNull($blog->published_at);
    }

    public function test_generated_slug_gets_a_suffix_when_already_taken(): void
    {
        $this->post('/admin/blogs', $this->payload());
        $this->post('/admin/blogs', $this->payload());

        $this->assertSame(['our-first-post', 'our-first-post-2'], Blog::orderBy('id')->pluck('slug')->all());
    }

    public function test_custom_slug_is_used_and_normalised(): void
    {
        $this->post('/admin/blogs', $this->payload(['slug' => 'My Custom Slug!']));

        $this->assertSame('my-custom-slug', Blog::first()->slug);
    }

    public function test_duplicate_custom_slug_is_rejected(): void
    {
        $this->post('/admin/blogs', $this->payload(['slug' => 'taken']));

        $this->post('/admin/blogs', $this->payload(['slug' => 'taken']))->assertSessionHasErrors('slug');
        $this->assertDatabaseCount('blogs', 1);
    }

    public function test_title_and_content_are_required(): void
    {
        $this->post('/admin/blogs', $this->payload(['title' => '', 'content' => '']))
            ->assertSessionHasErrors(['title', 'content', 'slug']);
    }

    public function test_big_image_gets_a_thumbnail(): void
    {
        $this->post('/admin/blogs', $this->payload([
            'image' => UploadedFile::fake()->image('big.jpg', 1600, 1200),
        ]));

        $blog = Blog::first();
        Storage::disk('uploads')->assertExists($blog->image);
        Storage::disk('uploads')->assertExists($blog->thumbnail);

        [$width, $height] = getimagesize(Storage::disk('uploads')->path($blog->thumbnail));
        $this->assertSame([600, 450], [$width, $height]);
    }

    public function test_small_image_is_stored_without_a_thumbnail(): void
    {
        $this->post('/admin/blogs', $this->payload([
            'image' => UploadedFile::fake()->image('small.png', 300, 200),
        ]));

        $blog = Blog::first();
        Storage::disk('uploads')->assertExists($blog->image);
        $this->assertNull($blog->thumbnail);
        $this->assertSame($blog->image_url, $blog->thumbnail_url);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->post('/admin/blogs', $this->payload([
            'image' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('image');
    }

    public function test_blog_can_be_updated_and_keeps_its_own_slug(): void
    {
        $this->post('/admin/blogs', $this->payload());
        $blog = Blog::first();

        $this->put("/admin/blogs/{$blog->id}", $this->payload([
            'title' => 'Renamed',
            'slug' => 'our-first-post',
            'is_published' => '0',
        ]))->assertRedirect('/admin/blogs')->assertSessionHasNoErrors();

        $blog->refresh();
        $this->assertSame('Renamed', $blog->title);
        $this->assertSame('our-first-post', $blog->slug);
        $this->assertFalse($blog->is_published);
        $this->assertNull($blog->published_at);
    }

    public function test_replacing_the_image_deletes_the_old_files(): void
    {
        $this->post('/admin/blogs', $this->payload([
            'image' => UploadedFile::fake()->image('old.jpg', 1600, 1200),
        ]));
        $blog = Blog::first();
        [$oldImage, $oldThumbnail] = [$blog->image, $blog->thumbnail];

        $this->put("/admin/blogs/{$blog->id}", $this->payload([
            'slug' => $blog->slug,
            'image' => UploadedFile::fake()->image('new.jpg', 900, 900),
        ]));

        $blog->refresh();
        Storage::disk('uploads')->assertMissing([$oldImage, $oldThumbnail]);
        Storage::disk('uploads')->assertExists([$blog->image, $blog->thumbnail]);
    }

    public function test_image_can_be_removed(): void
    {
        $this->post('/admin/blogs', $this->payload([
            'image' => UploadedFile::fake()->image('big.jpg', 1600, 1200),
        ]));
        $blog = Blog::first();
        [$image, $thumbnail] = [$blog->image, $blog->thumbnail];

        $this->put("/admin/blogs/{$blog->id}", $this->payload(['slug' => $blog->slug, 'remove_image' => '1']));

        $blog->refresh();
        $this->assertNull($blog->image);
        $this->assertNull($blog->thumbnail);
        Storage::disk('uploads')->assertMissing([$image, $thumbnail]);
    }

    public function test_deleting_a_blog_removes_its_images(): void
    {
        $this->post('/admin/blogs', $this->payload([
            'image' => UploadedFile::fake()->image('big.jpg', 1600, 1200),
        ]));
        $blog = Blog::first();

        $this->delete("/admin/blogs/{$blog->id}")->assertRedirect('/admin/blogs');

        $this->assertDatabaseCount('blogs', 0);
        Storage::disk('uploads')->assertMissing([$blog->image, $blog->thumbnail]);
    }
}
