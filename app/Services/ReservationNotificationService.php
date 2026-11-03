<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReservationNotificationService
{
    public function __construct(
        protected TelegramBotService $botService
    ) {}

    /**
     * Format reservation date for friendly reading.
     */
    protected function formatDate(string|Carbon $date): string
    {
        return Carbon::parse($date)->format('D, M d, Y');
    }

    /**
     * Notify Admin via Telegram when a customer creates a new reservation request.
     */
    public function notifyAdminNewReservation(Reservation $reservation): void
    {
        $adminChatId = Setting::get('telegram_admin_chat_id');
        if (!$adminChatId || !$this->botService->isConfigured()) {
            return;
        }

        $formattedDate = $this->formatDate($reservation->date);
        $slotLabel = $reservation->slot === 'LUNCH' ? 'Lunch Session' : 'Evening Acoustic Session';
        $contact = $reservation->phone . ($reservation->email ? " • {$reservation->email}" : '');
        $tgInfo = $reservation->telegramUsername ? " (@{$reservation->telegramUsername})" : '';

        $text = "🔔 <b>NEW TABLE RESERVATION REQUEST!</b>\n\n"
              . "🎫 <b>Code:</b> <code>{$reservation->code}</code>\n"
              . "👤 <b>Guest:</b> {$reservation->name}{$tgInfo}\n"
              . "📞 <b>Contact:</b> {$contact}\n"
              . "📅 <b>Date:</b> {$formattedDate} ({$slotLabel})\n"
              . "🪑 <b>Table:</b> Table {$reservation->tableCode} ({$reservation->guests} guests)\n";

        if ($reservation->request) {
            $text .= "📝 <b>Special Request:</b> " . htmlspecialchars($reservation->request) . "\n";
        }

        $text .= "⏳ <b>Status:</b> <b>PENDING ADMIN CONFIRMATION</b>\n\n"
              . "👉 <i>Please open the Admin Panel to confirm or cancel this booking.</i>";

        try {
            $this->botService->sendMessage($adminChatId, $text);
        } catch (\Throwable $e) {
            Log::warning("Could not send admin new reservation alert: " . $e->getMessage());
        }
    }

    /**
     * Notify Admin via Telegram when a customer cancels their booking.
     */
    public function notifyAdminCancelledReservation(Reservation $reservation): void
    {
        $adminChatId = Setting::get('telegram_admin_chat_id');
        if (!$adminChatId || !$this->botService->isConfigured()) {
            return;
        }

        $formattedDate = $this->formatDate($reservation->date);
        $text = "⚠️ <b>RESERVATION CANCELLED BY CUSTOMER</b>\n\n"
              . "🎫 <b>Code:</b> <code>{$reservation->code}</code>\n"
              . "👤 <b>Guest:</b> {$reservation->name} ({$reservation->phone})\n"
              . "📅 <b>Date:</b> {$formattedDate} ({$reservation->slot})\n"
              . "🪑 <b>Table:</b> Table {$reservation->tableCode}\n\n"
              . "<i>The table has been released and is available again.</i>";

        try {
            $this->botService->sendMessage($adminChatId, $text);
        } catch (\Throwable $e) {
            Log::warning("Could not send admin cancellation alert: " . $e->getMessage());
        }
    }

    /**
     * Notify Customer when their booking is CONFIRMED by Admin.
     */
    public function notifyCustomerBookingConfirmed(Reservation $reservation): void
    {
        $formattedDate = $this->formatDate($reservation->date);
        $slotLabel = $reservation->slot === 'LUNCH' ? 'Lunch Session (12:00 PM – 4:00 PM)' : 'Evening Acoustic Session (7:00 PM – 11:30 PM)';

        // 1. Send via Telegram Bot
        if (!empty($reservation->telegramUserId) && $this->botService->isConfigured()) {
            $text = "🎉 <b>YOUR RESERVATION IS CONFIRMED!</b>\n\n"
                  . "Selam <b>{$reservation->name}</b>! Great news — your table booking at Gura Bicha has been <b>CONFIRMED</b> by management.\n\n"
                  . "🎫 <b>Booking Code:</b> <code>{$reservation->code}</code>\n"
                  . "📅 <b>Date:</b> {$formattedDate}\n"
                  . "⏰ <b>Session:</b> {$slotLabel}\n"
                  . "🪑 <b>Table:</b> Table {$reservation->tableCode} ({$reservation->guests} guests)\n"
                  . "📍 <b>Location:</b> Mexico, Gura Bicha, Addis Ababa\n\n"
                  . "Show this booking code or your phone number upon arrival. We look forward to welcoming you! ✨";

            try {
                $this->botService->sendMessage($reservation->telegramUserId, $text);
            } catch (\Throwable $e) {
                Log::warning("Could not dispatch Telegram confirmation to customer {$reservation->telegramUserId}: " . $e->getMessage());
            }
        }

        // 2. Send via Email if provided
        if (!empty($reservation->email) && filter_var($reservation->email, FILTER_VALIDATE_EMAIL)) {
            try {
                $emailBody = "Selam {$reservation->name},\n\n"
                           . "Your table reservation at Gura Bicha Sports Bar & Acoustic Lounge has been CONFIRMED!\n\n"
                           . "Booking Code: {$reservation->code}\n"
                           . "Date: {$formattedDate}\n"
                           . "Session: {$slotLabel}\n"
                           . "Table: Table {$reservation->tableCode} ({$reservation->guests} guests)\n"
                           . "Location: Mexico, Gura Bicha, Addis Ababa\n\n"
                           . "Please present your booking code or phone number upon arrival.\n\n"
                           . "Warm regards,\nGura Bicha Sports Bar & Acoustic Lounge";

                Mail::raw($emailBody, function ($message) use ($reservation) {
                    $message->to($reservation->email)
                            ->subject("Table Booking Confirmed: {$reservation->code} — Gura Bicha");
                });
            } catch (\Throwable $e) {
                Log::warning("Could not dispatch confirmation email to {$reservation->email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Notify Customer when their booking is CANCELLED by Admin.
     */
    public function notifyCustomerBookingCancelled(Reservation $reservation, string $reason = ''): void
    {
        $formattedDate = $this->formatDate($reservation->date);

        // 1. Send via Telegram Bot
        if (!empty($reservation->telegramUserId) && $this->botService->isConfigured()) {
            $text = "ℹ️ <b>RESERVATION UPDATE — CANCELLED</b>\n\n"
                  . "Dear <b>{$reservation->name}</b>, your table booking (Code: <code>{$reservation->code}</code>) for {$formattedDate} at Gura Bicha has been cancelled.\n\n";

            if ($reason) {
                $text .= "<b>Reason:</b> " . htmlspecialchars($reason) . "\n\n";
            }

            $text .= "If you have any questions or would like to rebook another date, please call us at +251905888724 or use the Mini App.\n\n"
                   . "<i>We hope to host you soon!</i>";

            try {
                $this->botService->sendMessage($reservation->telegramUserId, $text);
            } catch (\Throwable $e) {
                Log::warning("Could not dispatch Telegram cancellation to customer {$reservation->telegramUserId}: " . $e->getMessage());
            }
        }

        // 2. Send via Email if provided
        if (!empty($reservation->email) && filter_var($reservation->email, FILTER_VALIDATE_EMAIL)) {
            try {
                $emailBody = "Dear {$reservation->name},\n\n"
                           . "Your table booking (Code: {$reservation->code}) for {$formattedDate} at Gura Bicha has been cancelled.\n\n"
                           . ($reason ? "Reason: {$reason}\n\n" : '')
                           . "If you wish to make a new booking, please visit our Mini App or contact us directly at +251905888724.\n\n"
                           . "Best regards,\nGura Bicha Sports Bar";

                Mail::raw($emailBody, function ($message) use ($reservation) {
                    $message->to($reservation->email)
                            ->subject("Booking Cancellation Notice: {$reservation->code} — Gura Bicha");
                });
            } catch (\Throwable $e) {
                Log::warning("Could not dispatch cancellation email to {$reservation->email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Notify Customer when their booking is DELETED by Admin.
     */
    public function notifyCustomerBookingDeleted(Reservation $reservation): void
    {
        $this->notifyCustomerBookingCancelled($reservation, 'Booking removed by venue administrator.');
    }
}
