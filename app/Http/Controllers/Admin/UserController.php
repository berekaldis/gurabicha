<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', 'in:SUPER_ADMIN,MANAGER,STAFF'],
            'password' => ['required', 'string', 'min:8'],
            'must_change_password' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'must_change_password' => $request->boolean('must_change_password', true),
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$validated['name']}' ({$validated['role']}) has been successfully created!");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:SUPER_ADMIN,MANAGER,STAFF'],
            'status' => ['required', 'in:ACTIVE,SUSPENDED'],
            'password' => ['nullable', 'string', 'min:8'],
            'must_change_password' => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'must_change_password' => $request->boolean('must_change_password'),
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' deleted.");
    }
}
