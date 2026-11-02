<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\SportMatch;
use App\Models\User;
use App\Models\VenueTable;
use Carbon\Carbon;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_unauthenticated_user_cannot_access_admin(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@gurabicha.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_admin_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@gurabicha.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Venue Dashboard');
        $response->assertSee('Venue Tables');
    }

    public function test_admin_can_manage_reservations(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();
        $date = Carbon::now('UTC')->addDays(5)->format('Y-m-d');

        // 1. Create reservation
        $res = $this->actingAs($admin)->post('/admin/reservations', [
            'date' => $date,
            'slot' => 'LUNCH',
            'tableCode' => 'T2',
            'guests' => 4,
            'name' => 'Walk-in Patron',
            'phone' => '0912345678',
            'status' => 'CONFIRMED',
        ]);

        $res->assertRedirect('/admin/reservations');
        $reservation = Reservation::where('phone', '0912345678')->first();
        $this->assertNotNull($reservation);
        $this->assertEquals('CONFIRMED', $reservation->status);

        // 2. Toggle status
        $toggle = $this->actingAs($admin)->post("/admin/reservations/{$reservation->id}/status", [
            'status' => 'CANCELLED',
        ]);
        $toggle->assertSessionHas('success');
        $this->assertEquals('CANCELLED', $reservation->fresh()->status);
    }

    public function test_admin_can_access_create_and_edit_reservation_pages(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        // 1. Access create reservation form with query parameters (table=T2&slot=EVENING)
        $createResponse = $this->actingAs($admin)->get('/admin/reservations/create?table=T2&slot=EVENING');
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Table Allocation');
        $createResponse->assertSee('T2');
        $createResponse->assertSee('EVENING');

        // 2. Access edit reservation form
        $reservation = Reservation::first();
        if (! $reservation) {
            $reservation = Reservation::create([
                'code' => 'GB-TEST01',
                'date' => Carbon::today()->format('Y-m-d'),
                'slot' => 'EVENING',
                'tableCode' => 'T1',
                'guests' => 2,
                'name' => 'Patron Test',
                'phone' => '0911000111',
                'status' => 'CONFIRMED',
            ]);
        }

        $editResponse = $this->actingAs($admin)->get("/admin/reservations/{$reservation->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Reservation');
        $editResponse->assertSee($reservation->name);
    }

    public function test_admin_can_manage_tables_and_events(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        // Tables
        $tablesView = $this->actingAs($admin)->get('/admin/tables');
        $tablesView->assertStatus(200);
        $tablesView->assertSee('T1');

        $addTable = $this->actingAs($admin)->post('/admin/tables', [
            'code' => 'T7',
            'zone' => 'Patio Garden',
            'capacity' => 4,
            'seats' => '2–4 people',
        ]);
        $addTable->assertRedirect('/admin/tables');
        $this->assertDatabaseHas('venue_tables', ['code' => 'T7']);

        // Events
        $eventsView = $this->actingAs($admin)->get('/admin/events');
        $eventsView->assertStatus(200);
        $eventsView->assertSee('Game & Groove');
    }

    public function test_admin_can_manage_matches_and_menu(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        // Matches
        $matchesView = $this->actingAs($admin)->get('/admin/matches');
        $matchesView->assertStatus(200);

        $match = SportMatch::first();
        $toggleMatch = $this->actingAs($admin)->post("/admin/matches/{$match->id}/toggle-featured");
        $toggleMatch->assertSessionHas('success');

        // Menu
        $menuView = $this->actingAs($admin)->get('/admin/menu');
        $menuView->assertStatus(200);
        $menuView->assertSee('Cheese Burger');

        $addMenu = $this->actingAs($admin)->post('/admin/menu', [
            'name' => 'Ethiopian Sambusa',
            'category' => 'FOOD',
            'price' => 180,
            'description' => 'Crispy lentil and beef pastry with awaze dipping sauce.',
            'popular' => 1,
        ]);
        $addMenu->assertRedirect('/admin/menu');
        $this->assertDatabaseHas('menu_items', ['name' => 'Ethiopian Sambusa']);
    }

    public function test_admin_can_manage_venue_settings(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $view = $this->actingAs($admin)->get('/admin/settings');
        $view->assertStatus(200);
        $view->assertSee('Chamber of Commerce');
        $view->assertSee('Saturday');

        $update = $this->actingAs($admin)->post('/admin/settings', [
            'venue_name' => 'Gura Bicha Sports Bar & Acoustic Lounge',
            'venue_location' => 'Addis Ababa, Ethiopia, Mexico inside Chamber of Commerce Building',
            'venue_phone' => '+251905888724',
            'venue_email' => 'contact@gurabicha.com',
            'venue_hours' => 'Daily 12:00 PM – 2:00 AM',
            'acoustic_day' => 'Live Acoustic Nights',
            'acoustic_time' => '7:00 PM – 11:30 PM',
            'google_maps_url' => 'https://maps.app.goo.gl/CK9ZByHcwZeUaHDK6',
            'google_maps_embed' => 'https://www.google.com/maps/embed?pb=test',
        ]);

        $update->assertRedirect('/admin/settings');
        $this->assertEquals('+251905888724', \App\Models\Setting::get('venue_phone'));
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::where('email', 'admin@gurabicha.com')->first();

        $response = $this->actingAs($admin)->post('/admin/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
