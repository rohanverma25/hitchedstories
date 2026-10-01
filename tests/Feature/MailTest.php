<?php

namespace Tests\Feature;

use App\Mail\NewLeadMail;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\Setting;
use App\Support\MailSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    private function signIn(): void
    {
        $this->actingAs(Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]), 'admin');
    }

    private function payload(array $overrides = []): array
    {
        return [
            'smtp_enabled' => '1',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => '465',
            'smtp_encryption' => 'ssl',
            'smtp_username' => 'mailer@example.com',
            'smtp_password' => 'app-password',
            'mail_from_address' => 'hello@example.com',
            'mail_from_name' => 'Hitched Studio',
            'lead_notify_enabled' => '1',
            'lead_notify_to' => 'owner@example.com, second@example.com',
            ...$overrides,
        ];
    }

    private function enquiry(): array
    {
        return ['name' => 'Ananya & Rohan', 'email' => 'ananya@example.com', 'message' => 'Hello'];
    }

    public function test_guests_cannot_reach_email_settings(): void
    {
        $this->get('/admin/mail')->assertRedirect('/admin/login');
        $this->put('/admin/mail', $this->payload())->assertRedirect('/admin/login');
        $this->post('/admin/mail/test', ['test_email' => 'a@example.com'])->assertRedirect('/admin/login');
        $this->assertDatabaseCount('settings', 0);
    }

    public function test_smtp_settings_are_saved_with_the_password_encrypted(): void
    {
        $this->signIn();

        $this->put('/admin/mail', $this->payload())->assertRedirect('/admin/mail')->assertSessionHasNoErrors();

        $this->assertSame('smtp.example.com', Setting::get('smtp_host'));
        $this->assertSame('1', Setting::get('smtp_enabled'));
        $this->assertNotSame('app-password', Setting::get('smtp_password'));
        $this->assertSame('app-password', Crypt::decryptString(Setting::get('smtp_password')));

        $this->get('/admin/mail')->assertOk()->assertSee('smtp.example.com')->assertDontSee('app-password')->assertSee('Saved. Type to replace it.');
    }

    public function test_blank_password_keeps_the_saved_one_and_it_can_be_removed(): void
    {
        $this->signIn();
        $this->put('/admin/mail', $this->payload());
        $saved = Setting::get('smtp_password');

        $this->put('/admin/mail', $this->payload(['smtp_password' => '']));
        $this->assertSame($saved, Setting::get('smtp_password'));

        $this->put('/admin/mail', $this->payload(['smtp_password' => '', 'clear_smtp_password' => '1']));
        $this->assertNull(Setting::get('smtp_password'));
    }

    public function test_turning_smtp_on_requires_host_port_and_sender(): void
    {
        $this->signIn();

        $this->put('/admin/mail', ['smtp_enabled' => '1', 'smtp_encryption' => 'tls'])
            ->assertSessionHasErrors(['smtp_host', 'smtp_port', 'mail_from_address']);

        $this->put('/admin/mail', ['smtp_encryption' => 'tls'])->assertSessionHasNoErrors();
        $this->assertSame('0', Setting::get('smtp_enabled'));
    }

    public function test_invalid_notification_addresses_are_rejected(): void
    {
        $this->signIn();

        $this->put('/admin/mail', $this->payload(['lead_notify_to' => 'owner@example.com, nope']))
            ->assertSessionHasErrors('lead_notify_to');
    }

    public function test_saved_smtp_settings_configure_the_mailer(): void
    {
        $this->signIn();
        $this->put('/admin/mail', $this->payload());

        $this->assertTrue(MailSettings::apply());

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));
        $this->assertSame('mailer@example.com', config('mail.mailers.smtp.username'));
        $this->assertSame('app-password', config('mail.mailers.smtp.password'));
        $this->assertSame('hello@example.com', config('mail.from.address'));
        $this->assertSame('Hitched Studio', config('mail.from.name'));
    }

    public function test_env_mailer_is_kept_while_smtp_is_off(): void
    {
        $before = config('mail.default');
        Setting::setMany(['smtp_enabled' => '0', 'smtp_host' => 'smtp.example.com']);

        $this->assertFalse(MailSettings::apply());
        $this->assertSame($before, config('mail.default'));
    }

    public function test_new_enquiry_emails_the_configured_recipients(): void
    {
        Mail::fake();
        Setting::setMany(['lead_notify_to' => 'owner@example.com, second@example.com']);

        $this->post('/enquiry', $this->enquiry())->assertSessionHasNoErrors();

        Mail::assertSent(NewLeadMail::class, fn (NewLeadMail $mail) => $mail->hasTo('owner@example.com')
            && $mail->hasTo('second@example.com')
            && $mail->hasReplyTo('ananya@example.com')
            && $mail->lead->name === 'Ananya & Rohan');
    }

    public function test_contact_email_is_used_when_no_recipient_is_set(): void
    {
        Mail::fake();
        Setting::setMany(['contact_email' => 'studio@example.com']);

        $this->post('/enquiry', $this->enquiry());

        Mail::assertSent(NewLeadMail::class, fn (NewLeadMail $mail) => $mail->hasTo('studio@example.com'));
    }

    public function test_no_email_when_notifications_are_off_or_nobody_is_set(): void
    {
        Mail::fake();

        $this->post('/enquiry', $this->enquiry());
        Mail::assertNothingSent();

        Setting::setMany(['lead_notify_to' => 'owner@example.com', 'lead_notify_enabled' => '0']);
        $this->post('/enquiry', $this->enquiry());
        Mail::assertNothingSent();

        $this->assertDatabaseCount('leads', 2);
    }

    public function test_the_lead_is_saved_even_when_sending_fails(): void
    {
        Setting::setMany([
            'lead_notify_to' => 'owner@example.com',
            'smtp_enabled' => '1',
            'smtp_host' => '127.0.0.1',
            'smtp_port' => '1',
            'smtp_encryption' => 'none',
            'mail_from_address' => 'hello@example.com',
        ]);

        $this->post('/enquiry', $this->enquiry())->assertRedirect()->assertSessionHas('enquiry_sent');

        $this->assertDatabaseCount('leads', 1);
    }

    public function test_test_email_reports_a_connection_failure(): void
    {
        $this->signIn();
        Setting::setMany([
            'smtp_enabled' => '1',
            'smtp_host' => '127.0.0.1',
            'smtp_port' => '1',
            'smtp_encryption' => 'none',
            'mail_from_address' => 'hello@example.com',
        ]);

        $this->post('/admin/mail/test', ['test_email' => 'me@example.com'])
            ->assertRedirect('/admin/mail')
            ->assertSessionHas('mail_error');
    }

    public function test_test_email_is_sent_and_validated(): void
    {
        $this->signIn();

        $this->post('/admin/mail/test', ['test_email' => 'nope'])->assertSessionHasErrors('test_email');

        $this->post('/admin/mail/test', ['test_email' => 'me@example.com'])
            ->assertRedirect('/admin/mail')
            ->assertSessionHas('status')
            ->assertSessionMissing('mail_error');
    }

    public function test_lead_count_still_works_with_the_email_page_in_the_menu(): void
    {
        $this->signIn();
        Lead::create(['name' => 'A', 'email' => 'a@example.com']);

        $this->get('/admin/mail')->assertOk()->assertSee('Email / SMTP');
    }
}
