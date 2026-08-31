// Server-side verification of Telegram Mini App initData.
//
// Telegram signs initData with the bot token: the signature is
// HMAC-SHA256(dataCheckString, HMAC-SHA256("WebAppData", botToken)).
// Set TELEGRAM_BOT_TOKEN in .env to enforce signature verification.
// Without a token (local dev), users are parsed but marked unverified.

import crypto from "node:crypto";

export interface TelegramUser {
  id: number;
  firstName?: string;
  lastName?: string;
  username?: string;
  photoUrl?: string;
  verified: boolean;
}

interface RawTgUser {
  id?: number | string;
  first_name?: string;
  last_name?: string;
  username?: string;
  photo_url?: string;
}

export function parseInitDataUser(initData: string): TelegramUser | null {
  const params = new URLSearchParams(initData);
  const raw = params.get("user");
  if (!raw) return null;
  try {
    const u = JSON.parse(raw) as RawTgUser;
    const id = typeof u.id === "string" ? Number(u.id) : u.id;
    if (!id || Number.isNaN(id)) return null;
    return {
      id,
      firstName: u.first_name,
      lastName: u.last_name,
      username: u.username,
      photoUrl: u.photo_url,
      verified: false,
    };
  } catch {
    return null;
  }
}

/**
 * Verifies the x-telegram-init-data header value.
 * Returns the user when present. When TELEGRAM_BOT_TOKEN is configured and
 * the hash is missing/invalid, returns null (caller decides to reject).
 */
export function verifyTelegramInitData(initData: string | null): TelegramUser | null {
  if (!initData) return null;
  const user = parseInitDataUser(initData);
  if (!user) return null;

  const token = process.env.TELEGRAM_BOT_TOKEN;
  if (!token) {
    // Dev mode: accept unverified (sandbox / local testing).
    return user;
  }

  const params = new URLSearchParams(initData);
  const hash = params.get("hash");
  if (!hash) return null;

  const pairs: string[] = [];
  params.forEach((value, key) => {
    if (key !== "hash" && key !== "signature") pairs.push(`${key}=${value}`);
  });
  pairs.sort();
  const dataCheckString = pairs.join("\n");

  const secretKey = crypto.createHmac("sha256", "WebAppData").update(token).digest();
  const computed = crypto.createHmac("sha256", secretKey).update(dataCheckString).digest("hex");

  if (computed !== hash) return null;
  return { ...user, verified: true };
}
