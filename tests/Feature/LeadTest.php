<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    private function enquiry(array $overrides = []): array
    {
        return [
            'name' => 'Ananya & Rohan',
            'email' => 'ananya@example.com',
            'phone' => '+91 98765 43210',
            'date' => now()->addYear()->format('Y-m-d'),
            'venue' => 'Udaipur',
            'message' => 'We would love a two-day shoot.',
            ...$overrides,
        ];
    }

    private function signIn(): void
    {
        $this->actingAs(Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]), 'admin');
    }

    public function test_enquiry_is_saved_as_a_new_lead(): void
    {
        $this->post('/enquiry', $this->enquiry())
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHas('enquiry_sent')
            ->assertSessionHasNoErrors();

        $lead = Lead::firstOrFail();
        $this->assertSame('Ananya & Rohan', $lead->name);
        $this->assertSame('new', $lead->status);
        $this->assertSame('Udaipur', $lead->venue);
        $this->assertSame(now()->addYear()->format('Y-m-d'), $lead->event_date->format('Y-m-d'));

        $this->get('/')->assertSee('will write back within two days');
    }

    public function test_only_name_and_email_are_required(): void
    {
        $this->post('/enquiry', ['name' => 'Meera', 'email' => 'meera@example.com'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('leads', 1);

        $this->post('/enquiry', [])->assertSessionHasErrors(['name', 'email']);
        $this->post('/enquiry', $this->enquiry(['email' => 'nope', 'date' => '2001-01-01']))->assertSessionHasErrors(['email', 'date']);
        $this->assertDatabaseCount('leads', 1);
    }

    public function test_invalid_submission_returns_to_the_contact_section_with_old_input(): void
    {
        $this->from('/')->post('/enquiry', $this->enquiry(['email' => 'nope']))
            ->assertRedirect(route('home').'#contact');

        $this->get('/')->assertSee('That email address does not look right.')->assertSee('value="Udaipur"', false);
    }

    public function test_bots_filling_the_honeypot_are_rejected(): void
    {
        $this->post('/enquiry', $this->enquiry(['website' => 'http://spam.example']))->assertSessionHasErrors('website');
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_submissions_are_rate_limited(): void
    {
        foreach (range(1, 5) as $i) {
            $this->post('/enquiry', $this->enquiry())->assertRedirect();
        }

        $this->post('/enquiry', $this->enquiry())->assertStatus(429);
        $this->assertDatabaseCount('leads', 5);
    }

    public function test_guests_cannot_see_leads(): void
    {
        $lead = Lead::create(['name' => 'A', 'email' => 'a@example.com']);

        $this->get('/admin/leads')->assertRedirect('/admin/login');
        $this->get("/admin/leads/{$lead->id}")->assertRedirect('/admin/login');
        $this->put("/admin/leads/{$lead->id}", ['status' => 'booked'])->assertRedirect('/admin/login');
        $this->delete("/admin/leads/{$lead->id}")->assertRedirect('/admin/login');
        $this->assertSame('new', $lead->fresh()->status);
    }

    public function test_admin_sees_leads_with_status_counts_and_filters(): void
    {
        $this->signIn();
        Lead::create(['name' => 'Fresh Couple', 'email' => 'fresh@example.com', 'venue' => 'Goa']);
        Lead::create(['name' => 'Booked Couple', 'email' => 'booked@example.com', 'status' => 'booked']);

        $this->get('/admin/leads')->assertOk()->assertSee('Fresh Couple')->assertSee('Booked Couple')->assertSee('Goa');
        $this->get('/admin/leads?status=booked')->assertOk()->assertSee('Booked Couple')->assertDontSee('Fresh Couple');
        $this->get('/admin/leads?q=fresh@')->assertOk()->assertSee('Fresh Couple')->assertDontSee('Booked Couple');
        $this->get('/admin/leads?q=zzz')->assertOk()->assertSee('No matching leads');
    }

    public function test_admin_can_view_update_and_delete_a_lead(): void
    {
        $this->signIn();
        $lead = Lead::create(['name' => 'Ananya & Rohan', 'email' => 'a@example.com', 'message' => 'Hello there']);

        $this->get("/admin/leads/{$lead->id}")->assertOk()->assertSee('Hello there');

        $this->put("/admin/leads/{$lead->id}", ['status' => 'contacted', 'notes' => 'Called on Monday'])
            ->assertRedirect("/admin/leads/{$lead->id}");
        $this->assertSame('contacted', $lead->fresh()->status);
        $this->assertSame('Called on Monday', $lead->fresh()->notes);

        $this->put("/admin/leads/{$lead->id}", ['status' => 'bogus'])->assertSessionHasErrors('status');

        $this->delete("/admin/leads/{$lead->id}")->assertRedirect('/admin/leads');
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_new_lead_count_shows_on_the_dashboard(): void
    {
        $this->signIn();
        Lead::create(['name' => 'A', 'email' => 'a@example.com']);

        $this->get('/admin')->assertOk()->assertSee('Leads (1 new)');
    }
}
