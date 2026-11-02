@extends('admin.layout', [
    'title' => 'User Management & Roles',
    'header' => 'Admin Users & Role Permissions',
    'subHeader' => 'Manage system administrators, venue managers, staff roles, and login security credentials.'
])

@section('content')
<div class="space-y-6" x-data="{
    showCreateModal: false,
    showEditModal: false,
    activeUser: null
}">

    <!-- TOP HEADER & ADD USER BUTTON -->
    <div class="p-6 rounded-2xl bg-admin-card border border-admin-border flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-lg">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-gold animate-pulse"></span>
                <h3 class="font-display text-xl font-bold uppercase text-white tracking-wider">System Users & Roles</h3>
            </div>
            <p class="text-xs text-zinc-300 mt-1">Super Admins have full access; Managers oversee bookings and operations; Staff manage floor tables.</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2.5 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition shadow-md shadow-gold/20 flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add Admin User</span>
        </button>
    </div>

    <!-- ROLE PERMISSIONS MATRIX CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-admin-card border border-gold/30 space-y-1.5">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-gold"></span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gold">Super Admin</h4>
            </div>
            <p class="text-xs text-zinc-300">Complete control over User Management, Settings, Tables, Menu, Events, Fixtures & Reservations.</p>
        </div>

        <div class="p-4 rounded-xl bg-admin-card border border-blue-500/30 space-y-1.5">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-400">Manager</h4>
            </div>
            <p class="text-xs text-zinc-300">Manage Reservations, Tables, Kitchen Menu, Football Matches, and Acoustic Events. Cannot edit users.</p>
        </div>

        <div class="p-4 rounded-xl bg-admin-card border border-emerald-500/30 space-y-1.5">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">Floor Staff</h4>
            </div>
            <p class="text-xs text-zinc-300">View and update live Reservations and Venue Table seating assignments for walk-in and booked guests.</p>
        </div>
    </div>

    <!-- USERS TABLE -->
    <div class="rounded-2xl bg-admin-card border border-admin-border overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-admin-panel text-zinc-300 border-b border-admin-border font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">User</th>
                        <th class="py-3.5 px-4">Email Address</th>
                        <th class="py-3.5 px-4">System Role</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Password Security</th>
                        <th class="py-3.5 px-4">Registered</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-admin-panel/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-gold/20 border border-gold/40 text-gold flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-white block">{{ $user->name }}</span>
                                        @if(Auth::id() === $user->id)
                                            <span class="text-[9px] text-gold uppercase font-bold">(You)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-zinc-300 font-mono">
                                {{ $user->email }}
                            </td>

                            <td class="py-3.5 px-4">
                                @if($user->role === 'SUPER_ADMIN')
                                    <span class="px-2.5 py-1 rounded-md bg-gold/20 border border-gold/40 text-gold font-bold text-[10px] tracking-wider uppercase">
                                        SUPER ADMIN
                                    </span>
                                @elseif($user->role === 'MANAGER')
                                    <span class="px-2.5 py-1 rounded-md bg-blue-950/60 border border-blue-500/40 text-blue-300 font-bold text-[10px] tracking-wider uppercase">
                                        MANAGER
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 font-bold text-[10px] tracking-wider uppercase">
                                        STAFF
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $user->status === 'ACTIVE' ? 'bg-emerald-900/60 text-emerald-300' : 'bg-red-900/60 text-red-300' }}">
                                    {{ $user->status }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                @if($user->must_change_password)
                                    <span class="px-2 py-0.5 rounded bg-amber-900/60 border border-amber-500/40 text-amber-200 text-[10px] font-semibold flex items-center w-max space-x-1">
                                        <span>⚠️ Must Change</span>
                                    </span>
                                @else
                                    <span class="text-zinc-400 text-[11px]">Active Permanent</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-zinc-400 text-[11px]">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="activeUser = {{ json_encode($user) }}; showEditModal = true" class="px-2.5 py-1 rounded bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold text-xs transition">
                                        Edit
                                    </button>

                                    @if(Auth::id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete user {{ $user->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-zinc-500 hover:text-red-400 transition" title="Delete User">
                                                ✕
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-zinc-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE USER MODAL -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Create Admin User</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Full Name *</label>
                    <input type="text" name="name" placeholder="Dawit Bekele" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Email Address *</label>
                    <input type="email" name="email" placeholder="dawit@gurabicha.com" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">System Role *</label>
                    <select name="role" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                        <option value="STAFF">STAFF — Floor & Reservations View</option>
                        <option value="MANAGER">MANAGER — Bookings, Menu, Tables, Matches</option>
                        <option value="SUPER_ADMIN">SUPER_ADMIN — Full System Access</option>
                    </select>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Initial Password (min 8 chars) *</label>
                    <input type="password" name="password" placeholder="••••••••" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div class="p-3 rounded-xl bg-admin-panel border border-admin-border flex items-start space-x-2">
                    <input type="checkbox" id="create_must_change" name="must_change_password" value="1" checked class="mt-0.5 rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="create_must_change" class="text-zinc-300 font-medium leading-relaxed">
                        <strong>Force password change on first login:</strong><br>
                        <span class="text-zinc-400 text-[11px]">User will be prompted to set a personal password immediately upon logging in.</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Edit Admin User</h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('admin/users') }}/' + (activeUser ? activeUser.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Full Name *</label>
                    <input type="text" name="name" :value="activeUser ? activeUser.name : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Email Address *</label>
                    <input type="email" name="email" :value="activeUser ? activeUser.email : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Role *</label>
                        <select name="role" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="STAFF" :selected="activeUser && activeUser.role === 'STAFF'">STAFF</option>
                            <option value="MANAGER" :selected="activeUser && activeUser.role === 'MANAGER'">MANAGER</option>
                            <option value="SUPER_ADMIN" :selected="activeUser && activeUser.role === 'SUPER_ADMIN'">SUPER_ADMIN</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Account Status *</label>
                        <select name="status" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="ACTIVE" :selected="activeUser && activeUser.status === 'ACTIVE'">ACTIVE</option>
                            <option value="SUSPENDED" :selected="activeUser && activeUser.status === 'SUSPENDED'">SUSPENDED</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Reset Password (leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="New password..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div class="p-3 rounded-xl bg-admin-panel border border-admin-border flex items-start space-x-2">
                    <input type="checkbox" id="edit_must_change" name="must_change_password" value="1" :checked="activeUser && activeUser.must_change_password" class="mt-0.5 rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="edit_must_change" class="text-zinc-300 font-medium">
                        Force user to change password on next login
                    </label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold">Update User</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
