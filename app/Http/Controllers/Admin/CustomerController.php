<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('club')) {
            $query->where('favorite_club', $request->club);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);
        $clubs = Customer::whereNotNull('favorite_club')->distinct()->pluck('favorite_club');

        return view('admin.customers.index', compact('customers', 'clubs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone'],
            'email' => ['nullable', 'email', 'max:100'],
            'favorite_club' => ['nullable', 'string', 'max:100'],
            'notify_matchday' => ['nullable', 'boolean'],
            'notify_scores' => ['nullable', 'boolean'],
            'notify_acoustic' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['notify_matchday'] = $request->boolean('notify_matchday', true);
        $validated['notify_scores'] = $request->boolean('notify_scores', true);
        $validated['notify_acoustic'] = $request->boolean('notify_acoustic', true);

        Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer '{$validated['name']}' added to Fan Club registry!");
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone,' . $customer->id],
            'email' => ['nullable', 'email', 'max:100'],
            'favorite_club' => ['nullable', 'string', 'max:100'],
            'notify_matchday' => ['nullable', 'boolean'],
            'notify_scores' => ['nullable', 'boolean'],
            'notify_acoustic' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['notify_matchday'] = $request->boolean('notify_matchday');
        $validated['notify_scores'] = $request->boolean('notify_scores');
        $validated['notify_acoustic'] = $request->boolean('notify_acoustic');

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer '{$customer->name}' profile updated.");
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', "Customer '{$name}' removed.");
    }

    public function sendAlert(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'club' => ['nullable', 'string'],
            'type' => ['required', 'in:MATCHDAY,SCORE,ACOUSTIC'],
            'message' => ['required', 'string', 'max:300'],
        ]);

        $query = Customer::query();

        if ($validated['type'] === 'MATCHDAY') {
            $query->where('notify_matchday', true);
            if (! empty($validated['club'])) {
                $query->where('favorite_club', $validated['club']);
            }
        } elseif ($validated['type'] === 'SCORE') {
            $query->where('notify_scores', true);
            if (! empty($validated['club'])) {
                $query->where('favorite_club', $validated['club']);
            }
        } else {
            $query->where('notify_acoustic', true);
        }

        $recipientCount = $query->count();

        return redirect()->route('admin.customers.index')
            ->with('success', "Notification dispatched to {$recipientCount} registered patrons!");
    }
}
