<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::orderBy('date', 'asc')->get();
        return view('admin.events.index', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:50'],
            'date' => ['required', 'date'],
            'startTime' => ['required', 'string', 'max:20'],
            'endTime' => ['required', 'string', 'max:20'],
            'imageUrl' => ['nullable', 'string', 'max:255'],
            'featured' => ['nullable', 'boolean'],
        ]);

        $validated['featured'] = $request->boolean('featured');

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$validated['title']}' created successfully!");
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:50'],
            'date' => ['required', 'date'],
            'startTime' => ['required', 'string', 'max:20'],
            'endTime' => ['required', 'string', 'max:20'],
            'imageUrl' => ['nullable', 'string', 'max:255'],
            'featured' => ['nullable', 'boolean'],
        ]);

        $validated['featured'] = $request->boolean('featured');

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$event->title}' updated successfully!");
    }

    public function destroy(Event $event): RedirectResponse
    {
        $title = $event->title;
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$title}' deleted.");
    }
}
