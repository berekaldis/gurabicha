<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\TelegramBroadcast;
use App\Services\TelegramBotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        protected TelegramBotService $botService
    ) {}

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
            'message' => ['required', 'string', 'max:500'],
        ]);

        $audience = match ($validated['type']) {
            'ACOUSTIC' => 'ACOUSTIC',
            default => (!empty($validated['club']) ? $validated['club'] : 'ALL'),
        };

        $typeLabel = match ($validated['type']) {
            'MATCHDAY' => '⚽ MATCHDAY ALERT',
            'SCORE' => '🏆 LIVE FINAL SCORE',
            'ACOUSTIC' => '🎸 SATURDAY ACOUSTIC NIGHT',
            default => '📢 GURA BICHA ALERT',
        };

        $clubSuffix = !empty($validated['club']) ? " • <b>{$validated['club']} Fans</b>" : '';
        $formattedText = "<b>{$typeLabel}</b>{$clubSuffix}\n\n"
            . htmlspecialchars($validated['message']) . "\n\n"
            . "📍 <i>Gura Bicha • Mexico, Addis Ababa</i>";

        $webAppUrl = Setting::get('telegram_webapp_url', (string) config('services.telegram.webapp_url', env('TELEGRAM_WEBAPP_URL', url('/'))));

        $replyMarkup = [
            'inline_keyboard' => [
                [
                    ['text' => '🚀 Launch Mini App', 'web_app' => ['url' => $webAppUrl]],
                    ['text' => '🌐 Open in Browser', 'url' => $webAppUrl],
                ],
            ],
        ];

        // Broadcast to all matching Telegram subscribers & customers
        $result = $this->botService->broadcast($formattedText, $replyMarkup, $audience);

        // Record history in TelegramBroadcast
        try {
            TelegramBroadcast::create([
                'title' => $typeLabel . (!empty($validated['club']) ? " ({$validated['club']})" : ''),
                'category' => match ($validated['type']) {
                    'MATCHDAY', 'SCORE' => 'FIXTURE',
                    'ACOUSTIC' => 'EVENT',
                    default => 'GENERAL',
                },
                'target_audience' => $audience,
                'message' => $validated['message'],
                'action_button_text' => 'Launch Mini App',
                'action_button_url' => $webAppUrl,
                'recipients_count' => $result['sent'],
                'status' => $result['sent'] > 0 ? 'SENT' : ($result['total'] === 0 ? 'NO_RECIPIENTS' : 'FAILED'),
                'sent_by_name' => Auth::user()->name ?? 'Admin',
            ]);
        } catch (\Throwable $e) {}

        if ($result['sent'] > 0) {
            return redirect()->route('admin.customers.index')
                ->with('success', "Match Alert broadcast successfully sent to {$result['sent']} patron(s) (Target: {$audience})!");
        }

        if ($result['total'] === 0) {
            return redirect()->route('admin.customers.index')
                ->with('warning', "Alert broadcast saved to history. No active Telegram subscribers currently match '{$audience}'. Patrons will receive alerts once they start @GuraBichaLiveBot.");
        }

        return redirect()->route('admin.customers.index')
            ->with('error', "Broadcast failed to send to {$result['failed']} subscriber(s). Please verify your Telegram Bot configuration.");
    }
}
