<?php

use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\TableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Gura Bicha Sports Bar API contract:
| - GET    /api/events
| - GET    /api/matches
| - GET    /api/menu
| - GET    /api/tables
| - GET    /api/reservations
| - POST   /api/reservations
| - DELETE /api/reservations/{id}
|
*/

Route::get('/events', [EventController::class, 'index']);
Route::get('/matches', [MatchController::class, 'index']);
Route::get('/menu', [MenuController::class, 'index']);
Route::get('/tables', [TableController::class, 'index']);
Route::get('/settings', function () {
    return response()->json([
        'venue_name' => \App\Models\Setting::get('venue_name', 'Gura Bicha Sports Bar & Lounge'),
        'venue_location' => \App\Models\Setting::get('venue_location', 'Addis Ababa, Ethiopia, Mexico inside Chamber of Commerce Building'),
        'venue_phone' => \App\Models\Setting::get('venue_phone', '+251905888724'),
        'venue_email' => \App\Models\Setting::get('venue_email', 'info@gurabicha.com'),
        'venue_hours' => \App\Models\Setting::get('venue_hours', 'Daily 12:00 PM – 2:00 AM (Live Acoustic 7:00 PM – 11:30 PM)'),
        'acoustic_day' => \App\Models\Setting::get('acoustic_day', 'Live Acoustic Nights'),
        'acoustic_time' => \App\Models\Setting::get('acoustic_time', '7:00 PM – 11:30 PM'),
        'google_maps_url' => \App\Models\Setting::get('google_maps_url', 'https://maps.app.goo.gl/CK9ZByHcwZeUaHDK6'),
    ]);
});

Route::get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

// Customer Info & Fan Profile
Route::get('/customer/profile', [CustomerProfileController::class, 'show']);
Route::post('/customer/profile', [CustomerProfileController::class, 'store']);

// Telegram Bot Webhook & Mini App Endpoints
Route::post('/telegram/webhook', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'webhook']);
Route::get('/telegram/info', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'info']);
Route::post('/telegram/set-webhook', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'setWebhook']);

// Live Internet Football Fixtures Sync Endpoint
Route::post('/fixtures/sync', function (\Illuminate\Http\Request $request, \App\Services\FixtureSyncService $syncService) {
    $league = $request->input('league', 'all');
    $dates = $request->input('dates');
    $result = $syncService->syncLiveFixtures($league, $dates);
    return response()->json(array_merge(['success' => true], $result));
});
