<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\SportMatch;
use App\Models\VenueTable;
use App\Services\ReservationNotificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationNotificationService $notificationService
    ) {}
    /**
     * Display a listing of reservations with filters and search.
     */
    public function index(Request $request): View
    {
        $query = Reservation::query();

        // Search by code, name, or phone
        if ($search = $request->query('search')) {
            $normalizedPhone = preg_replace('/\D+/', '', $search);
            $query->where(function ($q) use ($search, $normalizedPhone) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
                if (!empty($normalizedPhone)) {
                    $q->orWhere('phone', 'like', "%{$normalizedPhone}%");
                }
            });
        }

        // Filter by Date
        if ($date = $request->query('date')) {
            $query->whereDate('date', $date);
        }

        // Filter by Slot
        if ($slot = $request->query('slot')) {
            $query->where('slot', $slot);
        }

        // Filter by Status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by Table
        if ($tableCode = $request->query('table')) {
            $query->where('tableCode', $tableCode);
        }

        $reservations = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tables = VenueTable::orderBy('code')->get();
        $matches = SportMatch::where('kickoff', '>=', Carbon::now('UTC')->subDays(2))
            ->orderBy('kickoff')
            ->take(50)
            ->get();

        return view('admin.reservations.index', compact('reservations', 'tables', 'matches'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create(Request $request): View
    {
        $tables = VenueTable::orderBy('code')->get();
        $matches = SportMatch::where('kickoff', '>=', Carbon::now('UTC')->subDays(2))
            ->orderBy('kickoff')
            ->take(50)
            ->get();

        $selectedTable = $request->query('table', 'T1');
        $selectedSlot = $request->query('slot', 'EVENING');
        $selectedDate = $request->query('date', Carbon::now('Africa/Addis_Ababa')->toDateString());

        return view('admin.reservations.create', compact('tables', 'matches', 'selectedTable', 'selectedSlot', 'selectedDate'));
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit(Reservation $reservation): View
    {
        $tables = VenueTable::orderBy('code')->get();
        $matches = SportMatch::where('kickoff', '>=', Carbon::now('UTC')->subDays(2))
            ->orderBy('kickoff')
            ->take(50)
            ->get();

        return view('admin.reservations.edit', compact('reservation', 'tables', 'matches'));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'slot' => ['required', 'in:LUNCH,EVENING'],
            'tableCode' => ['required', 'exists:venue_tables,code'],
            'guests' => ['required', 'integer', 'min:1', 'max:12'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'min:7'],
            'email' => ['nullable', 'email'],
            'request' => ['nullable', 'string', 'max:500'],
            'matchId' => ['nullable', 'exists:matches,id'],
            'status' => ['required', 'in:CONFIRMED,PENDING,CANCELLED'],
        ]);

        $table = VenueTable::where('code', $validated['tableCode'])->first();
        if ($validated['guests'] > $table->capacity) {
            return back()->withInput()->with('error', "Table {$table->code} seats at most {$table->capacity} guests.");
        }

        // Check conflict if status is CONFIRMED
        if ($validated['status'] === 'CONFIRMED') {
            $conflict = Reservation::whereDate('date', $validated['date'])
                ->where('slot', $validated['slot'])
                ->where('tableCode', $validated['tableCode'])
                ->where('status', 'CONFIRMED')
                ->exists();

            if ($conflict) {
                return back()->withInput()->with('error', "Table {$validated['tableCode']} is already booked for that date and session.");
            }
        }

        // Generate Code
        $code = 'GB-' . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        while (Reservation::where('code', $code)->exists()) {
            $code = 'GB-' . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        }

        $matchLabel = null;
        if (!empty($validated['matchId'])) {
            $m = SportMatch::find($validated['matchId']);
            if ($m) {
                $matchLabel = "{$m->homeTeam} vs {$m->awayTeam} · {$m->competition} — {$m->stage}";
            }
        }

        $phone = preg_replace('/\D+/', '', $validated['phone']);

        Reservation::create([
            'code' => $code,
            'date' => $validated['date'],
            'slot' => $validated['slot'],
            'tableCode' => $validated['tableCode'],
            'guests' => $validated['guests'],
            'name' => $validated['name'],
            'phone' => $phone,
            'email' => $validated['email'] ?? null,
            'request' => $validated['request'] ?? null,
            'matchId' => $validated['matchId'] ?? null,
            'matchLabel' => $matchLabel,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.reservations.index')
            ->with('success', "Reservation {$code} created successfully!");
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'slot' => ['required', 'in:LUNCH,EVENING'],
            'tableCode' => ['required', 'exists:venue_tables,code'],
            'guests' => ['required', 'integer', 'min:1', 'max:12'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'min:7'],
            'email' => ['nullable', 'email'],
            'request' => ['nullable', 'string', 'max:500'],
            'matchId' => ['nullable', 'exists:matches,id'],
            'status' => ['required', 'in:CONFIRMED,PENDING,CANCELLED'],
        ]);

        $table = VenueTable::where('code', $validated['tableCode'])->first();
        if ($validated['guests'] > $table->capacity) {
            return back()->withInput()->with('error', "Table {$table->code} seats at most {$table->capacity} guests.");
        }

        // Check conflict if status is CONFIRMED and slot/table changed
        if ($validated['status'] === 'CONFIRMED') {
            $conflict = Reservation::whereDate('date', $validated['date'])
                ->where('slot', $validated['slot'])
                ->where('tableCode', $validated['tableCode'])
                ->where('status', 'CONFIRMED')
                ->where('id', '!=', $reservation->id)
                ->exists();

            if ($conflict) {
                return back()->withInput()->with('error', "Table {$validated['tableCode']} is already booked for that date and session.");
            }
        }

        $matchLabel = $reservation->matchLabel;
        if (!empty($validated['matchId'])) {
            $m = SportMatch::find($validated['matchId']);
            if ($m) {
                $matchLabel = "{$m->homeTeam} vs {$m->awayTeam} · {$m->competition} — {$m->stage}";
            }
        } elseif (array_key_exists('matchId', $validated) && empty($validated['matchId'])) {
            $matchLabel = null;
        }

        $phone = preg_replace('/\D+/', '', $validated['phone']);

        $reservation->update([
            'date' => $validated['date'],
            'slot' => $validated['slot'],
            'tableCode' => $validated['tableCode'],
            'guests' => $validated['guests'],
            'name' => $validated['name'],
            'phone' => $phone,
            'email' => $validated['email'] ?? null,
            'request' => $validated['request'] ?? null,
            'matchId' => $validated['matchId'] ?? null,
            'matchLabel' => $matchLabel,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.reservations.index')
            ->with('success', "Reservation {$reservation->code} updated successfully!");
    }

    /**
     * Quick status update (Confirm, Pending, or Cancel).
     */
    public function updateStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:CONFIRMED,PENDING,CANCELLED'],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $reservation->status;

        if ($newStatus === 'CONFIRMED') {
            $conflict = Reservation::whereDate('date', $reservation->date)
                ->where('slot', $reservation->slot)
                ->where('tableCode', $reservation->tableCode)
                ->where('status', 'CONFIRMED')
                ->where('id', '!=', $reservation->id)
                ->exists();

            if ($conflict) {
                return back()->with('error', "Cannot confirm: Table {$reservation->tableCode} is already booked for this session.");
            }
        }

        $reservation->update(['status' => $newStatus]);

        // Send notifications via Telegram or Email
        if ($newStatus === 'CONFIRMED' && $oldStatus !== 'CONFIRMED') {
            $this->notificationService->notifyCustomerBookingConfirmed($reservation);
        } elseif ($newStatus === 'CANCELLED' && $oldStatus !== 'CANCELLED') {
            $this->notificationService->notifyCustomerBookingCancelled($reservation);
        }

        return back()->with('success', "Reservation {$reservation->code} status changed to {$newStatus}. Patron has been notified!");
    }

    /**
     * Remove the specified reservation.
     */
    public function destroy(Reservation $reservation): RedirectResponse
    {
        $code = $reservation->code;

        // Notify customer that booking was removed
        $this->notificationService->notifyCustomerBookingDeleted($reservation);

        $reservation->delete();

        return redirect()->route('admin.reservations.index')
            ->with('success', "Reservation {$code} deleted and patron notified.");
    }
}
