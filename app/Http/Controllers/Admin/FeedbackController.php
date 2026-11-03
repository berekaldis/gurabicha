<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    /**
     * Display a listing of customer feedback and suggestions.
     */
    public function index(Request $request): View
    {
        $query = Feedback::with('customer')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('phone', 'like', $search)
                  ->orWhere('telegram_username', 'like', $search)
                  ->orWhere('message', 'like', $search);
            });
        }

        $feedbacks = $query->paginate(15)->withQueryString();

        $counts = [
            'total' => Feedback::count(),
            'new' => Feedback::where('status', 'NEW')->count(),
            'reviewed' => Feedback::where('status', 'REVIEWED')->count(),
            'resolved' => Feedback::where('status', 'RESOLVED')->count(),
        ];

        return view('admin.feedbacks.index', compact('feedbacks', 'counts'));
    }

    /**
     * Update feedback status and optional admin notes.
     */
    public function updateStatus(Request $request, Feedback $feedback): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:NEW,REVIEWED,RESOLVED',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $feedback->update($validated);

        return redirect()->back()->with('success', 'Feedback status updated successfully.');
    }

    /**
     * Remove the specified feedback.
     */
    public function destroy(Feedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return redirect()->back()->with('success', 'Feedback deleted successfully.');
    }
}
