<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\SportMatch;
use App\Models\VenueTable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuraBichaApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Seed tables, menu, events, matches
        $this->artisan('db:seed');
    }

    public function test_homepage_renders(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('GURA BICHA');
        $response->assertSee('Game & Groove', false);
    }

    public function test_get_events_returns_upcoming_events(): void
    {
        $response = $this->getJson('/api/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'events' => [
                    '*' => ['id', 'title', 'description', 'category', 'date', 'startTime', 'endTime', 'featured']
                ]
            ]);

        $this->assertNotEmpty($response->json('events'));
    }

    public function test_get_matches_returns_matches(): void
    {
        $response = $this->getJson('/api/matches');
        $response->assertStatus(200)
            ->assertJsonStructure(['matches']);

        // Check year 2026 filter
        $yearResponse = $this->getJson('/api/matches?year=2026');
        $yearResponse->assertStatus(200);
        $this->assertCount(563, $yearResponse->json('matches'));

        // Check invalid year
        $invalidYear = $this->getJson('/api/matches?year=abc');
        $invalidYear->assertStatus(400)
            ->assertJson(['error' => 'Invalid year. Must be a 4-digit year.']);
    }

    public function test_get_menu_returns_ordered_items(): void
    {
        $response = $this->getJson('/api/menu');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'menu' => [
                    '*' => ['id', 'name', 'category', 'price', 'description', 'popular']
                ]
            ]);

        $menu = $response->json('menu');
        $this->assertCount(13, $menu);

        // Verify categories order: FOOD first, then DRINKS (no SHISHA)
        $categories = array_column($menu, 'category');
        $this->assertNotContains('SHISHA', $categories);
        $firstFood = array_search('FOOD', $categories);
        $firstDrinks = array_search('DRINKS', $categories);

        $this->assertTrue($firstFood < $firstDrinks);
    }

    public function test_get_settings_returns_venue_details(): void
    {
        $response = $this->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'venue_location' => 'Addis Ababa, Ethiopia, Mexico inside Chamber of Commerce Building',
            ]);
        $this->assertNotEmpty($response->json('acoustic_day'));
    }

    public function test_get_tables_and_reservation_flow(): void
    {
        $bookingDate = Carbon::now('UTC')->addDays(10)->format('Y-m-d');
        $slot = 'EVENING';

        // 1. Initial tables query
        $tablesRes = $this->getJson("/api/tables?date={$bookingDate}&slot={$slot}");
        $tablesRes->assertStatus(200)
            ->assertJson(['remaining' => 6]);

        // 2. Validation tests for POST /api/reservations
        // Past date
        $pastRes = $this->postJson('/api/reservations', [
            'date' => '2020-01-01',
            'slot' => 'LUNCH',
            'tableCode' => 'T1',
            'guests' => 2,
            'name' => 'Abebe',
            'phone' => '0911223344',
        ]);
        $pastRes->assertStatus(400)
            ->assertJson(['error' => 'Cannot book a date in the past.']);

        // Over capacity
        $overCapacity = $this->postJson('/api/reservations', [
            'date' => $bookingDate,
            'slot' => $slot,
            'tableCode' => 'T1', // capacity is 4
            'guests' => 6,
            'name' => 'Abebe',
            'phone' => '0911223344',
        ]);
        $overCapacity->assertStatus(400)
            ->assertJson(['error' => 'Table T1 seats at most 4 guests.']);

        // 3. Successful booking
        $validRes = $this->postJson('/api/reservations', [
            'date' => $bookingDate,
            'slot' => $slot,
            'tableCode' => 'T1',
            'guests' => 3,
            'name' => 'Abebe Kebede',
            'phone' => '+251 911 223 344',
            'email' => 'abebe@example.com',
            'request' => 'Table near the big screen please',
        ]);

        $validRes->assertStatus(201)
            ->assertJsonStructure([
                'reservation' => ['id', 'code', 'tableCode', 'status', 'name', 'phone']
            ]);

        $reservation = $validRes->json('reservation');
        $this->assertStringStartsWith('GB-', $reservation['code']);
        $this->assertEquals('CONFIRMED', $reservation['status']);
        $this->assertEquals('251911223344', $reservation['phone']); // Normalized digits

        // 4. Duplicate booking returns 409 Conflict
        $dupRes = $this->postJson('/api/reservations', [
            'date' => $bookingDate,
            'slot' => $slot,
            'tableCode' => 'T1',
            'guests' => 2,
            'name' => 'Dawit',
            'phone' => '0922334455',
        ]);
        $dupRes->assertStatus(409)
            ->assertJson(['error' => 'Table T1 is no longer available for that slot. Please pick another.']);

        // 5. Remaining tables is now 5
        $tablesRes2 = $this->getJson("/api/tables?date={$bookingDate}&slot={$slot}");
        $tablesRes2->assertStatus(200)
            ->assertJson(['remaining' => 5]);

        // 6. Lookup by phone
        $lookup = $this->getJson('/api/reservations?phone=0911223344');
        // Because phone was saved as '251911223344', let's test lookup with international prefix or local
        $lookup2 = $this->getJson('/api/reservations?phone=251911');
        $lookup2->assertStatus(200);
        $this->assertNotEmpty($lookup2->json('reservations'));

        // 7. ICS Calendar download
        $icsRes = $this->get("/ics/{$reservation['code']}");
        $icsRes->assertStatus(200);
        $this->assertStringContainsString('BEGIN:VCALENDAR', $icsRes->getContent());
        $this->assertStringContainsString($reservation['code'], $icsRes->getContent());

        // 8. Cancel reservation
        $cancelRes = $this->deleteJson("/api/reservations/{$reservation['id']}");
        $cancelRes->assertStatus(200)
            ->assertJsonPath('reservation.status', 'CANCELLED');

        // 9. Table T1 is available again, remaining is 6
        $tablesRes3 = $this->getJson("/api/tables?date={$bookingDate}&slot={$slot}");
        $tablesRes3->assertStatus(200)
            ->assertJson(['remaining' => 6]);
    }
}
