<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
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
            'site_name' => 'Hitched',
            'tagline' => 'Weddings, beautifully managed',
            'about_content' => 'We plan weddings.',
            'footer_text' => '© Hitched',
            'contact_email' => 'hello@hitched.test',
            'contact_phone' => '+91 98765 43210',
            'whatsapp_number' => '+91 98765 43210',
            'address' => "12 Rose Lane\nMumbai",
            'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=1"></iframe>',
            'facebook_url' => 'https://facebook.com/hitched',
            'instagram_url' => 'https://instagram.com/hitched',
            'head_scripts' => '<script>window.dataLayer = [];</script>',
            'body_scripts' => '<script src="https://example.com/chat.js"></script>',
            ...$overrides,
        ];
    }

    public function test_guests_cannot_manage_settings(): void
    {
        auth('admin')->logout();

        $this->get('/admin/settings')->assertRedirect('/admin/login');
        $this->put('/admin/settings', $this->payload())->assertRedirect('/admin/login');
        $this->assertDatabaseCount('settings', 0);
    }

    public function test_settings_page_is_displayed_with_saved_values(): void
    {
        $this->get('/admin/settings')->assertOk();

        $this->put('/admin/settings', $this->payload());

        $this->get('/admin/settings')
            ->assertOk()
            ->assertSee('hello@hitched.test')
            ->assertSee('https://instagram.com/hitched')
            ->assertSee('window.dataLayer = [];')
            ->assertDontSee('<script>window.dataLayer = [];</script>', false);
    }

    public function test_content_contact_social_and_scripts_are_saved(): void
    {
        $this->put('/admin/settings', $this->payload())
            ->assertRedirect('/admin/settings')
            ->assertSessionHasNoErrors();

        $this->assertSame('Hitched', Setting::get('site_name'));
        $this->assertSame('hello@hitched.test', Setting::get('contact_email'));
        $this->assertSame("12 Rose Lane\nMumbai", Setting::get('address'));
        $this->assertSame('https://facebook.com/hitched', Setting::get('facebook_url'));
        $this->assertSame('<script>window.dataLayer = [];</script>', Setting::get('head_scripts'));
        $this->assertSame('<script src="https://example.com/chat.js"></script>', Setting::get('body_scripts'));
    }

    public function test_settings_can_be_changed_and_cleared(): void
    {
        $this->put('/admin/settings', $this->payload());
        $this->put('/admin/settings', $this->payload(['site_name' => 'Hitched Weddings', 'facebook_url' => '']));

        $this->assertSame('Hitched Weddings', Setting::get('site_name'));
        $this->assertNull(Setting::get('facebook_url'));
        $this->assertSame(1, Setting::where('key', 'site_name')->count());
    }

    public function test_invalid_email_and_links_are_rejected(): void
    {
        $this->put('/admin/settings', $this->payload([
            'contact_email' => 'not-an-email',
            'instagram_url' => 'instagram.com/hitched',
        ]))->assertSessionHasErrors(['contact_email', 'instagram_url']);

        $this->assertDatabaseCount('settings', 0);
    }

    public function test_logo_can_be_uploaded_replaced_and_removed(): void
    {
        $this->put('/admin/settings', $this->payload(['logo' => UploadedFile::fake()->image('logo.png', 400, 120)]));
        $first = Setting::get('logo');
        $this->assertStringStartsWith('settings/', $first);
        Storage::disk('uploads')->assertExists($first);
        $this->get('/admin/settings')->assertSee(Setting::logoUrl());

        $this->put('/admin/settings', $this->payload(['logo' => UploadedFile::fake()->image('new.png', 400, 120)]));
        $second = Setting::get('logo');
        Storage::disk('uploads')->assertMissing($first);
        Storage::disk('uploads')->assertExists($second);

        $this->put('/admin/settings', $this->payload());
        $this->assertSame($second, Setting::get('logo'));

        $this->put('/admin/settings', $this->payload(['remove_logo' => '1']));
        $this->assertNull(Setting::get('logo'));
        Storage::disk('uploads')->assertMissing($second);
    }

    public function test_logo_must_be_an_image_file(): void
    {
        $this->put('/admin/settings', $this->payload([
            'logo' => UploadedFile::fake()->create('logo.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('logo');
    }
}
