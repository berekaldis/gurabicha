@extends('admin.layout', [
    'title' => 'Change Temporary Password',
    'header' => 'Security: Change Password',
    'subHeader' => 'You must set a new permanent password before continuing to access the management console.'
])

@section('content')
<div class="max-w-md mx-auto pt-4">
    <div class="p-6 rounded-3xl bg-admin-card border border-gold/40 shadow-2xl relative overflow-hidden">
        <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <h3 class="font-display text-xl font-bold uppercase text-white tracking-wide">Set New Password</h3>
        <p class="text-xs text-zinc-300 mt-1">Please enter your temporary password followed by your new personal password (at least 8 characters).</p>

        @if(session('warning'))
            <div class="mt-4 p-3.5 rounded-xl bg-amber-950/60 border border-amber-600/50 text-amber-200 text-xs">
                {{ session('warning') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}" class="mt-6 space-y-4 text-xs">
            @csrf

            <div>
                <label class="block text-zinc-300 font-semibold mb-1">Current (Temporary) Password *</label>
                <input type="password" name="current_password" required autocomplete="current-password"
                    class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-gold">
                @error('current_password')
                    <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-zinc-300 font-semibold mb-1">New Password (min 8 characters) *</label>
                <input type="password" name="password" required autocomplete="new-password"
                    class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-gold">
                @error('password')
                    <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-zinc-300 font-semibold mb-1">Confirm New Password *</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-gold">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-sm transition shadow-lg shadow-gold/25">
                    Update Password & Proceed
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
