<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $phone = $request->query('phone');
        if (! $phone) {
            return response()->json(['error' => 'Phone number is required.'], 400);
        }

        $customer = Customer::where('phone', trim($phone))->first();

        return response()->json(['customer' => $customer]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'favorite_club' => ['nullable', 'string', 'max:100'],
            'notify_matchday' => ['nullable', 'boolean'],
            'notify_scores' => ['nullable', 'boolean'],
            'notify_acoustic' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $phone = trim($validated['phone']);

        $customer = Customer::updateOrCreate(
            ['phone' => $phone],
            [
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'favorite_club' => $validated['favorite_club'] ?? null,
                'notify_matchday' => $request->boolean('notify_matchday', true),
                'notify_scores' => $request->boolean('notify_scores', true),
                'notify_acoustic' => $request->boolean('notify_acoustic', true),
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Fan Club profile saved successfully!',
            'customer' => $customer,
        ]);
    }
}
