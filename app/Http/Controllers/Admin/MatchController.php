<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $query = SportMatch::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('homeTeam', 'like', "%{$search}%")
                  ->orWhere('awayTeam', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        if ($comp = $request->query('competition')) {
            $query->where('competition', $comp);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($month = $request->query('month')) {
            $query->whereMonth('kickoff', $month);
        }

        $matches = $query->orderBy('kickoff', 'asc')->paginate(20)->withQueryString();

        return view('admin.matches.index', compact('matches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'homeTeam' => ['required', 'string', 'max:50'],
            'awayTeam' => ['required', 'string', 'max:50'],
            'competition' => ['required', 'string', 'max:50'],
            'stage' => ['nullable', 'string', 'max:50'],
            'venue' => ['nullable', 'string', 'max:100'],
            'kickoff' => ['required', 'date'],
            'status' => ['required', 'in:SCHEDULED,LIVE,FINISHED'],
            'homeScore' => ['nullable', 'integer', 'min:0'],
            'awayScore' => ['nullable', 'integer', 'min:0'],
            'featured' => ['nullable', 'boolean'],
        ]);

        $validated['featured'] = $request->boolean('featured');

        if ($validated['featured']) {
            SportMatch::where('featured', true)->update(['featured' => false]);
        }

        SportMatch::create($validated);

        return redirect()->route('admin.matches.index')
            ->with('success', "Match {$validated['homeTeam']} vs {$validated['awayTeam']} created!");
    }

    public function update(Request $request, SportMatch $match): RedirectResponse
    {
        $validated = $request->validate([
            'homeTeam' => ['required', 'string', 'max:50'],
            'awayTeam' => ['required', 'string', 'max:50'],
            'competition' => ['required', 'string', 'max:50'],
            'stage' => ['nullable', 'string', 'max:50'],
            'venue' => ['nullable', 'string', 'max:100'],
            'kickoff' => ['required', 'date'],
            'status' => ['required', 'in:SCHEDULED,LIVE,FINISHED'],
            'homeScore' => ['nullable', 'integer', 'min:0'],
            'awayScore' => ['nullable', 'integer', 'min:0'],
            'featured' => ['nullable', 'boolean'],
        ]);

        $validated['featured'] = $request->boolean('featured');

        if ($validated['featured']) {
            SportMatch::where('id', '!=', $match->id)->where('featured', true)->update(['featured' => false]);
        }

        $match->update($validated);

        return redirect()->route('admin.matches.index')
            ->with('success', "Match {$match->homeTeam} vs {$match->awayTeam} updated!");
    }

    public function toggleFeatured(SportMatch $match): RedirectResponse
    {
        if (!$match->featured) {
            SportMatch::where('featured', true)->update(['featured' => false]);
            $match->update(['featured' => true]);
            $msg = "Match {$match->homeTeam} vs {$match->awayTeam} is now the homepage featured match.";
        } else {
            $match->update(['featured' => false]);
            $msg = "Match {$match->homeTeam} vs {$match->awayTeam} unfeatured.";
        }

        return back()->with('success', $msg);
    }

    public function syncFromInternet(\App\Services\FixtureSyncService $service): RedirectResponse
    {
        $result = $service->syncLiveFixtures('all');
        $synced = $result['synced'] ?? 0;
        $live = $result['live'] ?? 0;
        $total = $result['total_fetched'] ?? 0;

        return redirect()->route('admin.matches.index')
            ->with('success', "⚡ Internet Sync Complete! Fetched {$total} matches from live football scoreboard API, updated/created {$synced} fixtures ({$live} live ongoing).");
    }

    public function destroy(SportMatch $match): RedirectResponse
    {
        $label = "{$match->homeTeam} vs {$match->awayTeam}";
        $match->delete();

        return redirect()->route('admin.matches.index')
            ->with('success', "Match {$label} deleted.");
    }
}
