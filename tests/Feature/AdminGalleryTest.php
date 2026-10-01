<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGalleryTest extends TestCase
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

    private function upload(int $count = 3): void
    {
        $this->post('/admin/gallery', [
            'images' => array_map(fn ($i) => UploadedFile::fake()->image("photo{$i}.jpg", 1600, 1200), range(1, $count)),
        ]);
    }

    public function test_guests_cannot_manage_the_gallery(): void
    {
        auth('admin')->logout();

        $this->get('/admin/gallery')->assertRedirect('/admin/login');
        $this->post('/admin/gallery', ['images' => [UploadedFile::fake()->image('a.jpg')]])->assertRedirect('/admin/login');
        $this->assertDatabaseCount('gallery_images', 0);
    }

    public function test_gallery_page_is_displayed(): void
    {
        $this->get('/admin/gallery')->assertOk()->assertSee('The gallery is empty');

        $this->upload(2);

        $this->get('/admin/gallery')->assertOk()->assertSee(GalleryImage::first()->thumbnail_url);
        $this->get('/admin')->assertOk()->assertSee('Gallery images');
    }

    public function test_multiple_images_are_uploaded_at_once_with_thumbnails(): void
    {
        $this->post('/admin/gallery', [
            'images' => [
                UploadedFile::fake()->image('one.jpg', 1600, 1200),
                UploadedFile::fake()->image('two.png', 900, 1800),
                UploadedFile::fake()->image('small.jpg', 300, 200),
            ],
        ])->assertRedirect('/admin/gallery')->assertSessionHas('status', '3 images uploaded.');

        $images = GalleryImage::orderBy('id')->get();
        $this->assertCount(3, $images);

        foreach ($images as $image) {
            $this->assertStringStartsWith('gallery/', $image->image);
            Storage::disk('uploads')->assertExists($image->image);
        }

        $this->assertStringStartsWith('gallery/thumbs/', $images[0]->thumbnail);
        $this->assertSame([600, 450], array_slice(getimagesize(Storage::disk('uploads')->path($images[0]->thumbnail)), 0, 2));
        $this->assertSame([300, 600], array_slice(getimagesize(Storage::disk('uploads')->path($images[1]->thumbnail)), 0, 2));
        $this->assertNull($images[2]->thumbnail);
    }

    public function test_upload_requires_at_least_one_image(): void
    {
        $this->post('/admin/gallery', [])->assertSessionHasErrors('images');
    }

    public function test_upload_is_rejected_when_any_file_is_not_an_image(): void
    {
        $this->post('/admin/gallery', [
            'images' => [
                UploadedFile::fake()->image('ok.jpg', 800, 800),
                UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
            ],
        ])->assertSessionHasErrors('images.1');

        $this->assertDatabaseCount('gallery_images', 0);
        $this->assertSame([], Storage::disk('uploads')->allFiles());
    }

    public function test_more_than_the_maximum_number_of_images_is_rejected(): void
    {
        $this->post('/admin/gallery', [
            'images' => array_map(fn ($i) => UploadedFile::fake()->image("p{$i}.jpg", 10, 10), range(1, 21)),
        ])->assertSessionHasErrors('images');

        $this->assertDatabaseCount('gallery_images', 0);
    }

    public function test_single_image_can_be_deleted_with_its_files(): void
    {
        $this->upload(2);
        [$first, $second] = GalleryImage::orderBy('id')->get()->all();

        $this->delete("/admin/gallery/{$first->id}")->assertRedirect();

        $this->assertDatabaseCount('gallery_images', 1);
        Storage::disk('uploads')->assertMissing([$first->image, $first->thumbnail]);
        Storage::disk('uploads')->assertExists([$second->image, $second->thumbnail]);
    }

    public function test_selected_images_can_be_deleted_together(): void
    {
        $this->upload(3);
        [$first, $second, $third] = GalleryImage::orderBy('id')->get()->all();

        $this->delete('/admin/gallery', ['ids' => [$first->id, $third->id]])
            ->assertRedirect('/admin/gallery')
            ->assertSessionHas('status', '2 images deleted.');

        $this->assertSame([$second->id], GalleryImage::pluck('id')->all());
        Storage::disk('uploads')->assertMissing([$first->image, $first->thumbnail, $third->image, $third->thumbnail]);
        Storage::disk('uploads')->assertExists([$second->image, $second->thumbnail]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        $this->upload(1);

        $this->delete('/admin/gallery', [])->assertSessionHasErrors('ids');
        $this->assertDatabaseCount('gallery_images', 1);
    }
}
