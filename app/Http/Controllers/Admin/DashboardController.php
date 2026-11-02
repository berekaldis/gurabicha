<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Event;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\SportMatch;
use App\Models\VenueTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::now('Africa/Addis_Ababa')->format('Y-m-d');

        // High-level statistics
        $stats = [
            'total_reservations' => Reservation::count(),
            'confirmed_reservations' => Reservation::where('status', 'CONFIRMED')->count(),
            'today_reservations' => Reservation::whereDate('date', $today)->where('status', 'CONFIRMED')->count(),
            'total_tables' => VenueTable::count(),
            'total_menu_items' => MenuItem::count(),
            'total_matches' => SportMatch::count(),
            'total_events' => Event::count(),
            'total_customers' => Customer::count(),
        ];

        // Top supported football clubs by fans
        $topClubs = Customer::select('favorite_club', DB::raw('count(*) as count'))
            ->whereNotNull('favorite_club')
            ->where('favorite_club', '!=', '')
            ->groupBy('favorite_club')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Tables status for today (both Lunch & Evening)
        $tables = VenueTable::orderBy('code')->get();
        $todayLunchBookings = Reservation::whereDate('date', $today)
            ->where('slot', 'LUNCH')
            ->where('status', 'CONFIRMED')
            ->get()
            ->keyBy('tableCode');

        $todayEveningBookings = Reservation::whereDate('date', $today)
            ->where('slot', 'EVENING')
            ->where('status', 'CONFIRMED')
            ->get()
            ->keyBy('tableCode');

        // Recent reservations
        $recentReservations = Reservation::orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Next upcoming matches
        $upcomingMatches = SportMatch::where('kickoff', '>=', Carbon::now('UTC')->subHours(2))
            ->orderBy('kickoff', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'tables',
            'today',
            'todayLunchBookings',
            'todayEveningBookings',
            'recentReservations',
            'upcomingMatches',
            'topClubs'
        ));
    }
}
