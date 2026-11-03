<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\Admin\MenuItemController as AdminMenuItemController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\TelegramConfigController as AdminTelegramConfigController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VenueSettingController as AdminVenueSettingController;
use App\Http\Controllers\Admin\VenueTableController as AdminVenueTableController;
use App\Http\Controllers\Web\CalendarExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web & Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $order = ['FOOD', 'PIZZA', 'DRINKS'];
    $menu = \App\Models\MenuItem::all()->sort(function ($a, $b) use ($order) {
        $ca = array_search($a->category, $order, true);
        $cb = array_search($b->category, $order, true);
        $ca = $ca === false ? 999 : $ca;
        $cb = $cb === false ? 999 : $cb;
        if ($ca !== $cb) {
            return $ca <=> $cb;
        }
        return strcasecmp($a->name, $b->name);
    })->values();
    $events = \App\Models\Event::where('status', 'ACTIVE')->orderBy('date', 'asc')->get();
    return view('app', compact('menu', 'events'));
})->name('home');

Route::get('/ics/{code}', [CalendarExportController::class, 'download'])->name('reservation.ics');

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Protected Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Password Change (accessible even if must_change_password is true)
    Route::get('/change-password', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');

    // Admin routes guarded by forced password change check
    Route::middleware('force.password')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Reservations
        Route::resource('reservations', AdminReservationController::class);
        Route::post('reservations/{reservation}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.status');

        // Tables
        Route::resource('tables', AdminVenueTableController::class)->except(['show', 'create', 'edit']);

        // Events
        Route::resource('events', AdminEventController::class)->except(['show', 'create', 'edit']);

        // Matches
        Route::post('matches/sync-internet', [AdminMatchController::class, 'syncFromInternet'])->name('matches.sync');
        Route::resource('matches', AdminMatchController::class)->except(['show', 'create', 'edit']);
        Route::post('matches/{match}/toggle-featured', [AdminMatchController::class, 'toggleFeatured'])->name('matches.toggle-featured');

        // Menu
        Route::post('menu/board-update', [AdminMenuItemController::class, 'updateMenuBoard'])->name('menu.board.update');
        Route::resource('menu', AdminMenuItemController::class)->except(['show', 'create', 'edit']);

        // Customer Registry & Fan Club Alerts
        Route::resource('customers', AdminCustomerController::class)->except(['show', 'create', 'edit']);
        Route::post('customers/broadcast-alert', [AdminCustomerController::class, 'sendAlert'])->name('customers.alert');

        // Customer Feedback & Suggestions
        Route::get('feedbacks', [AdminFeedbackController::class, 'index'])->name('feedbacks.index');
        Route::patch('feedbacks/{feedback}/status', [AdminFeedbackController::class, 'updateStatus'])->name('feedbacks.status');
        Route::delete('feedbacks/{feedback}', [AdminFeedbackController::class, 'destroy'])->name('feedbacks.destroy');

        // Telegram Bot Configuration & Broadcast Announcements
        Route::get('telegram', [AdminTelegramConfigController::class, 'index'])->name('telegram.index');
        Route::post('telegram/settings', [AdminTelegramConfigController::class, 'updateSettings'])->name('telegram.settings.update');
        Route::post('telegram/test-notification', [AdminTelegramConfigController::class, 'sendTestNotification'])->name('telegram.test-notification');
        Route::post('telegram/webhook/set', [AdminTelegramConfigController::class, 'setWebhook'])->name('telegram.webhook.set');
        Route::post('telegram/webhook/delete', [AdminTelegramConfigController::class, 'deleteWebhook'])->name('telegram.webhook.delete');
        Route::post('telegram/broadcast', [AdminTelegramConfigController::class, 'broadcast'])->name('telegram.broadcast');

        // Venue Settings & QR Code
        Route::get('settings', [AdminVenueSettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminVenueSettingController::class, 'update'])->name('settings.update');

        // User Management & Roles (Super Admin Only)
        Route::middleware('role:SUPER_ADMIN')->group(function () {
            Route::resource('users', AdminUserController::class)->except(['show', 'create', 'edit']);
        });
    });
});
