<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueSettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'venue_name' => Setting::get('venue_name', 'Gura Bicha Sports Bar & Lounge'),
            'venue_location' => Setting::get('venue_location', 'Addis Ababa, Ethiopia, Mexico inside Chamber of Commerce Building'),
            'venue_phone' => Setting::get('venue_phone', '+251905888724'),
            'venue_email' => Setting::get('venue_email', 'info@gurabicha.com'),
            'venue_hours' => Setting::get('venue_hours', 'Daily 12:00 PM – 2:00 AM (Live Acoustic 7:00 PM – 11:30 PM)'),
            'acoustic_day' => Setting::get('acoustic_day', 'Live Acoustic Nights'),
            'acoustic_time' => Setting::get('acoustic_time', '7:00 PM – 11:30 PM'),
            'google_maps_url' => Setting::get('google_maps_url', 'https://maps.app.goo.gl/CK9ZByHcwZeUaHDK6'),
            'google_maps_embed' => Setting::get('google_maps_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.540118350811!2d38.7441584!3d9.0143899!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b8591e5c54c33%3A0xb1a9c37e6f851eb!2sAddis%20Ababa%20Chamber%20of%20Commerce!5e0!3m2!1sen!2set!4v1700000000000'),
            'telegram_bot_token' => Setting::get('telegram_bot_token', (string) config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN', ''))),
            'telegram_bot_username' => Setting::get('telegram_bot_username', (string) config('services.telegram.bot_username', env('TELEGRAM_BOT_USERNAME', 'GuraBichaAddisBot'))),
            'telegram_admin_chat_id' => Setting::get('telegram_admin_chat_id', ''),
        ];

        $menuPublicUrl = url('/#menu');

        return view('admin.settings.index', compact('settings', 'menuPublicUrl'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'venue_name' => ['sometimes', 'required', 'string', 'max:150'],
            'venue_location' => ['sometimes', 'required', 'string', 'max:255'],
            'venue_phone' => ['sometimes', 'required', 'string', 'max:50'],
            'venue_email' => ['sometimes', 'required', 'email', 'max:100'],
            'venue_hours' => ['sometimes', 'required', 'string', 'max:255'],
            'acoustic_day' => ['sometimes', 'required', 'string', 'max:50'],
            'acoustic_time' => ['sometimes', 'required', 'string', 'max:100'],
            'google_maps_url' => ['sometimes', 'required', 'url', 'max:500'],
            'google_maps_embed' => ['nullable', 'string', 'max:1000'],
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
            'telegram_bot_username' => ['nullable', 'string', 'max:100'],
            'telegram_admin_chat_id' => ['nullable', 'string', 'max:50'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}
