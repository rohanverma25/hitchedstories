<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminServiceTest extends TestCase
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
            'title' => 'Wedding Photography',
            'slug' => '',
            'short_description' => 'Full-day coverage.',
            'description' => '<p>We capture every moment.</p>',
            'price' => '25000.50',
            'sort_order' => '2',
            'meta_title' => 'Photography meta',
            'meta_description' => 'Meta description.',
            'meta_keywords' => 'wedding, photography',
            'is_published' => '1',
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_services(): void
    {
        auth('admin')->logout();

        $this->get('/admin/services')->assertRedirect('/admin/login');
        $this->post('/admin/services', $this->payload())->assertRedirect('/admin/login');
        $this->assertDatabaseCount('services', 0);
    }

    public function test_service_pages_are_displayed(): void
    {
        $this->post('/admin/services', $this->payload());
        $service = Service::first();

        $this->get('/admin/services')->assertOk()->assertSee('Wedding Photography')->assertSee('25,000.50');
        $this->get('/admin/services/create')->assertOk();
        $this->get("/admin/services/{$service->id}/edit")->assertOk()->assertSee('Photography meta');
        $this->get('/admin')->assertOk()->assertSee('Services');
    }

    public function test_service_is_created_with_a_generated_slug(): void
    {
        $this->post('/admin/services', $this->payload())->assertRedirect('/admin/services');

        $service = Service::first();
        $this->assertSame('wedding-photography', $service->slug);
        $this->assertSame('25000.50', $service->price);
        $this->assertSame(2, $service->sort_order);
        $this->assertSame('Photography meta', $service->meta_title);
        $this->assertTrue($service->is_published);
    }

    public function test_price_and_display_order_are_optional(): void
    {
        $this->post('/admin/services', $this->payload(['price' => '', 'sort_order' => '']))->assertSessionHasNoErrors();

        $service = Service::first();
        $this->assertNull($service->price);
        $this->assertSame(0, $service->sort_order);
    }

    public function test_slugs_are_unique_per_service(): void
    {
        $this->post('/admin/services', $this->payload());
        $this->post('/admin/services', $this->payload());
        $this->assertSame(['wedding-photography', 'wedding-photography-2'], Service::orderBy('id')->pluck('slug')->all());

        $this->post('/admin/services', $this->payload(['slug' => 'Wedding Photography']))->assertSessionHasErrors('slug');
        $this->assertDatabaseCount('services', 2);
    }

    public function test_invalid_input_is_rejected(): void
    {
        $this->post('/admin/services', $this->payload(['title' => '', 'description' => '', 'price' => '-5']))
            ->assertSessionHasErrors(['title', 'description', 'price', 'slug']);

        $this->post('/admin/services', $this->payload([
            'image' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('image');
    }

    public function test_big_image_gets_a_thumbnail_in_the_services_folder(): void
    {
        $this->post('/admin/services', $this->payload([
            'image' => UploadedFile::fake()->image('big.jpg', 1600, 1200),
        ]));

        $service = Service::first();
        $this->assertStringStartsWith('services/', $service->image);
        $this->assertStringStartsWith('services/thumbs/', $service->thumbnail);
        Storage::disk('uploads')->assertExists([$service->image, $service->thumbnail]);
    }

    public function test_service_can_be_updated(): void
    {
        $this->post('/admin/services', $this->payload(['image' => UploadedFile::fake()->image('big.jpg', 1600, 1200)]));
        $service = Service::first();
        [$image, $thumbnail] = [$service->image, $service->thumbnail];

        $this->put("/admin/services/{$service->id}", $this->payload([
            'title' => 'Renamed',
            'slug' => 'wedding-photography',
            'is_published' => '0',
            'remove_image' => '1',
        ]))->assertRedirect('/admin/services')->assertSessionHasNoErrors();

        $service->refresh();
        $this->assertSame('Renamed', $service->title);
        $this->assertSame('wedding-photography', $service->slug);
        $this->assertFalse($service->is_published);
        $this->assertNull($service->image);
        Storage::disk('uploads')->assertMissing([$image, $thumbnail]);
    }

    public function test_deleting_a_service_removes_its_images(): void
    {
        $this->post('/admin/services', $this->payload(['image' => UploadedFile::fake()->image('big.jpg', 1600, 1200)]));
        $service = Service::first();

        $this->delete("/admin/services/{$service->id}")->assertRedirect('/admin/services');

        $this->assertDatabaseCount('services', 0);
        Storage::disk('uploads')->assertMissing([$service->image, $service->thumbnail]);
    }
}
