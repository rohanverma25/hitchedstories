<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminReviewTest extends TestCase
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
            'name' => 'Priya Sharma',
            'designation' => 'Bride, Mumbai',
            'rating' => '5',
            'review' => 'Everything was perfect.',
            'is_published' => '1',
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_reviews(): void
    {
        auth('admin')->logout();

        $this->get('/admin/reviews')->assertRedirect('/admin/login');
        $this->post('/admin/reviews', $this->payload())->assertRedirect('/admin/login');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_pages_are_displayed(): void
    {
        $this->post('/admin/reviews', $this->payload());
        $review = Review::first();

        $this->get('/admin/reviews')->assertOk()->assertSee('Priya Sharma')->assertSee('Bride, Mumbai');
        $this->get('/admin/reviews/create')->assertOk();
        $this->get("/admin/reviews/{$review->id}/edit")->assertOk()->assertSee('Everything was perfect.');
        $this->get('/admin')->assertOk()->assertSee('Reviews');
    }

    public function test_review_can_be_created(): void
    {
        $this->post('/admin/reviews', $this->payload())->assertRedirect('/admin/reviews');

        $review = Review::first();
        $this->assertSame('Priya Sharma', $review->name);
        $this->assertSame('Bride, Mumbai', $review->designation);
        $this->assertSame(5, $review->rating);
        $this->assertTrue($review->is_published);
    }

    public function test_name_rating_and_review_are_required(): void
    {
        $this->post('/admin/reviews', $this->payload(['name' => '', 'rating' => '', 'review' => '']))
            ->assertSessionHasErrors(['name', 'rating', 'review']);
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $this->post('/admin/reviews', $this->payload(['rating' => '6']))->assertSessionHasErrors('rating');
        $this->post('/admin/reviews', $this->payload(['rating' => '0']))->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_can_be_updated(): void
    {
        $this->post('/admin/reviews', $this->payload());
        $review = Review::first();

        $this->put("/admin/reviews/{$review->id}", $this->payload([
            'name' => 'Rahul Mehta',
            'rating' => '3',
            'is_published' => '0',
        ]))->assertRedirect('/admin/reviews')->assertSessionHasNoErrors();

        $review->refresh();
        $this->assertSame('Rahul Mehta', $review->name);
        $this->assertSame(3, $review->rating);
        $this->assertFalse($review->is_published);
    }

    public function test_photo_can_be_uploaded_replaced_and_removed(): void
    {
        $this->post('/admin/reviews', $this->payload(['photo' => UploadedFile::fake()->image('a.jpg', 300, 300)]));
        $review = Review::first();
        $first = $review->photo;
        Storage::disk('uploads')->assertExists($first);

        $this->put("/admin/reviews/{$review->id}", $this->payload(['photo' => UploadedFile::fake()->image('b.jpg', 300, 300)]));
        $second = $review->refresh()->photo;
        Storage::disk('uploads')->assertMissing($first);
        Storage::disk('uploads')->assertExists($second);

        $this->put("/admin/reviews/{$review->id}", $this->payload(['remove_photo' => '1']));
        $this->assertNull($review->refresh()->photo);
        Storage::disk('uploads')->assertMissing($second);
    }

    public function test_non_image_photo_is_rejected(): void
    {
        $this->post('/admin/reviews', $this->payload([
            'photo' => UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('photo');
    }

    public function test_deleting_a_review_removes_its_photo(): void
    {
        $this->post('/admin/reviews', $this->payload(['photo' => UploadedFile::fake()->image('a.jpg', 300, 300)]));
        $review = Review::first();

        $this->delete("/admin/reviews/{$review->id}")->assertRedirect('/admin/reviews');

        $this->assertDatabaseCount('reviews', 0);
        Storage::disk('uploads')->assertMissing($review->photo);
    }
}
