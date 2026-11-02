<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\SportMatch;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.layout', function ($view) {
            try {
                if (Schema::hasTable('reservations') && Schema::hasTable('customers') && Schema::hasTable('menu_items')) {
                    $view->with('sidebarCounts', [
                        'reservations' => Reservation::where('status', 'CONFIRMED')->count(),
                        'customers' => Customer::count(),
                        'menu' => MenuItem::count(),
                        'matches' => SportMatch::count(),
                    ]);
                }
            } catch (\Throwable $e) {
                $view->with('sidebarCounts', [
                    'reservations' => 0,
                    'customers' => 0,
                    'menu' => 0,
                    'matches' => 0,
                ]);
            }
        });
    }
}
