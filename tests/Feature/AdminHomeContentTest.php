<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\GalleryImage;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminHomeContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('uploads');
    }

    private function signIn(): void
    {
        $this->actingAs(Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]), 'admin');
    }

    public function test_guests_cannot_edit_page_content(): void
    {
        $this->get('/admin/content')->assertRedirect('/admin/login');
        $this->put('/admin/content', ['intro_text' => 'Nope'])->assertRedirect('/admin/login');
        $this->assertDatabaseCount('settings', 0);
    }

    public function test_site_uses_original_copy_until_content_is_saved(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Honest, unhurried photographs for')
            ->assertSee('We do not pose moments. We wait for them.')
            ->assertSee('Pre-Wedding Shoots')
            ->assertSee('images/site/hero-light.jpg', false);
    }

    public function test_saved_text_and_headings_appear_on_the_site(): void
    {
        $this->signIn();

        $this->put('/admin/content', [
            'intro_heading' => 'Warm photographs for *every love story*',
            'quote_text' => 'Every frame is a promise.',
            'about_text_1' => '{name} is small.',
            'cta_button' => 'Say hello',
        ])->assertRedirect('/admin/content')->assertSessionHasNoErrors();

        $this->get('/')
            ->assertOk()
            ->assertSee('Warm photographs for <em>every love story</em>', false)
            ->assertSee('Every frame is a promise.')
            ->assertSee('Say hello')
            ->assertDontSee('We do not pose moments');
    }

    public function test_clearing_a_field_restores_the_original_copy(): void
    {
        $this->signIn();

        $this->put('/admin/content', ['quote_text' => 'Temporary quote']);
        $this->put('/admin/content', ['quote_text' => '']);

        $this->assertNull(Setting::get('home_quote_text'));
        $this->get('/')->assertSee('We do not pose moments. We wait for them.');
    }

    public function test_heading_html_is_escaped(): void
    {
        $this->signIn();

        $this->put('/admin/content', ['intro_heading' => '<script>alert(1)</script> *bold*']);

        $this->get('/')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; <em>bold</em>', false);
    }

    public function test_images_can_be_uploaded_replaced_and_reset(): void
    {
        $this->signIn();

        $this->put('/admin/content', ['hero_image' => UploadedFile::fake()->image('a.jpg', 800, 600)])->assertSessionHasNoErrors();
        $first = Setting::get('home_hero_image');
        Storage::disk('uploads')->assertExists($first);
        $this->get('/')->assertSee('uploads/'.$first, false);

        $this->put('/admin/content', ['hero_image' => UploadedFile::fake()->image('b.jpg', 800, 600)]);
        $second = Setting::get('home_hero_image');
        Storage::disk('uploads')->assertMissing($first);
        Storage::disk('uploads')->assertExists($second);

        $this->put('/admin/content', []);
        $this->assertSame($second, Setting::get('home_hero_image'));

        $this->put('/admin/content', ['remove_hero_image' => '1']);
        $this->assertNull(Setting::get('home_hero_image'));
        Storage::disk('uploads')->assertMissing($second);
        $this->get('/')->assertSee('images/site/hero-light.jpg', false);
    }

    public function test_non_images_and_overlong_text_are_rejected(): void
    {
        $this->signIn();

        $this->put('/admin/content', [
            'about_image' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
            'quote_text' => str_repeat('a', 300),
        ])->assertSessionHasErrors(['about_image', 'quote_text']);
    }

    public function test_content_page_is_displayed(): void
    {
        $this->signIn();

        $this->get('/admin/content')->assertOk()->assertSee('Page content')->assertSee('Call to action');
    }

    public function test_published_services_and_gallery_replace_the_samples(): void
    {
        Service::create(['title' => 'Drone Coverage', 'slug' => 'drone-coverage', 'short_description' => 'Aerial views.', 'description' => 'Details.', 'sort_order' => 2, 'is_published' => true]);
        Service::create(['title' => 'Album Design', 'slug' => 'album-design', 'short_description' => 'Printed keepsakes.', 'description' => 'Details.', 'sort_order' => 1, 'is_published' => true]);
        Service::create(['title' => 'Hidden Service', 'slug' => 'hidden', 'short_description' => 'Draft.', 'description' => 'Details.', 'description' => 'Details.', 'is_published' => false]);
        GalleryImage::create(['image' => 'gallery/one.jpg']);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Album Design', 'Drone Coverage'])
            ->assertSee('Printed keepsakes.')
            ->assertDontSee('Hidden Service')
            ->assertDontSee('Pre-Wedding Shoots')
            ->assertSee('uploads/gallery/one.jpg', false)
            ->assertDontSee('gallery-01.jpg', false);
    }

    public function test_settings_drive_the_layout_and_contact_section(): void
    {
        Setting::setMany([
            'site_name' => 'Lumière Studio',
            'tagline' => 'Films for forever',
            'footer_text' => '© Lumière, all rights kept.',
            'contact_email' => 'hi@lumiere.test',
            'contact_phone' => '+91 99999 11111',
            'address' => '12 Rose Lane',
            'instagram_url' => 'https://instagram.com/lumiere',
            'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=abc" width="600"></iframe>',
            'head_scripts' => '<meta name="verify" content="abc123">',
            'body_scripts' => '<script>window.chat = 1;</script>',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Lumière Studio · Films for forever</title>', false)
            ->assertSee('© Lumière, all rights kept.')
            ->assertSee('mailto:hi@lumiere.test', false)
            ->assertSee('tel:+919999911111', false)
            ->assertSee('12 Rose Lane')
            ->assertSee('https://instagram.com/lumiere', false)
            ->assertSee('src="https://www.google.com/maps/embed?pb=abc"', false)
            ->assertSee('<meta name="verify" content="abc123">', false)
            ->assertSee('window.chat = 1;', false)
            ->assertDontSee('hello@example.com');
    }

    public function test_social_icons_are_hidden_when_no_links_are_set(): void
    {
        $this->get('/')->assertOk()->assertDontSee('class="social"', false);
    }
}
