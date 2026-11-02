<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\VenueTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueTableController extends Controller
{
    public function index(): View
    {
        $tables = VenueTable::withCount(['reservations' => function ($q) {
            $q->where('status', 'CONFIRMED');
        }])->orderBy('code')->get();

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:venue_tables,code'],
            'zone' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'seats' => ['required', 'string', 'max:50'],
        ]);

        VenueTable::create($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', "Table {$validated['code']} added successfully!");
    }

    public function update(Request $request, VenueTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:venue_tables,code,' . $table->id],
            'zone' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'seats' => ['required', 'string', 'max:50'],
        ]);

        $table->update($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', "Table {$table->code} updated successfully!");
    }

    public function destroy(VenueTable $table): RedirectResponse
    {
        // Check active reservations
        $activeBookings = Reservation::where('tableCode', $table->code)
            ->where('status', 'CONFIRMED')
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', "Cannot delete Table {$table->code}: it has {$activeBookings} confirmed reservations.");
        }

        $code = $table->code;
        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', "Table {$code} deleted.");
    }
}
