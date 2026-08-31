"use client";

// Telegram Mini App integration: provider context, haptic feedback helpers,
// BackButton / MainButton hooks and share helpers. Everything degrades
// gracefully when the app runs in a plain browser.

import {
  createContext,
  useContext,
  useEffect,
  useMemo,
  useRef,
  useState,
  type ReactNode,
} from "react";

/* ---------------- WebApp typings (subset we use) ---------------- */

interface TgWebAppUser {
  id: number;
  first_name?: string;
  last_name?: string;
  username?: string;
  photo_url?: string;
}

interface TgBackButton {
  show(): void;
  hide(): void;
  onClick(cb: () => void): void;
  offClick(cb: () => void): void;
}

interface TgMainButton {
  setText(text: string): void;
  setParams(params: {
    color?: string;
    text_color?: string;
    is_active?: boolean;
    is_visible?: boolean;
  }): void;
  show(): void;
  hide(): void;
  enable(): void;
  disable(): void;
  onClick(cb: () => void): void;
  offClick(cb: () => void): void;
}

interface TgHapticFeedback {
  impactOccurred(style: "light" | "medium" | "heavy" | "rigid" | "soft"): void;
  notificationOccurred(type: "error" | "success" | "warning"): void;
  selectionChanged(): void;
}

interface TgCloudStorage {
  setItem(key: string, value: string, callback?: (error: null) => void): void;
  getItem(key: string, callback?: (error: null, value?: string) => void): void;
  removeItem(key: string, callback?: (error: null) => void): void;
}

export interface TgWebApp {
  initData: string;
  initDataUnsafe: { user?: TgWebAppUser; start_param?: string };
  version: string;
  colorScheme: "light" | "dark";
  ready(): void;
  expand(): void;
  close(): void;
  setHeaderColor?(color: string): void;
  setBackgroundColor?(color: string): void;
  disableVerticalSwipes?(): void;
  enableClosingConfirmation?(): void;
  disableClosingConfirmation?(): void;
  openTelegramLink(url: string): void;
  openLink(url: string, options?: { try_instant_view?: boolean }): void;
  showAlert?(message: string, callback?: () => void): void;
  HapticFeedback?: TgHapticFeedback;
  BackButton?: TgBackButton;
  MainButton?: TgMainButton;
  CloudStorage?: TgCloudStorage;
  isVersionAtLeast?(version: string): boolean;
}

export function getWebApp(): TgWebApp | null {
  if (typeof window === "undefined") return null;
  const tg = (window as unknown as { Telegram?: { WebApp?: TgWebApp } }).Telegram;
  return tg?.WebApp ?? null;
}

export function inTelegram(): boolean {
  const wa = getWebApp();
  return Boolean(wa && wa.initData !== "");
}

/* ---------------- provider ---------------- */

interface TelegramUser {
  id: number;
  firstName: string;
  lastName?: string;
  username?: string;
  photoUrl?: string;
}

interface TelegramCtx {
  /** True when the app runs inside Telegram (with real initData). */
  available: boolean;
  user: TelegramUser | null;
}

const TelegramContext = createContext<TelegramCtx>({ available: false, user: null });

export function TelegramProvider({ children }: { children: ReactNode }) {
  const [ctx, setCtx] = useState<TelegramCtx>({ available: false, user: null });
  const interceptorsRef = useRef<BackInterceptor[]>([]);
  const backCtx = useMemo<BackCtx>(
    () => ({
      runInterceptors: () => {
        // Last registered wins — the deepest open UI handles back first.
        for (let i = interceptorsRef.current.length - 1; i >= 0; i--) {
          try {
            if (interceptorsRef.current[i]()) return true;
          } catch {
            // broken interceptor — keep unwinding
          }
        }
        return false;
      },
      registerBackInterceptor: (fn) => {
        interceptorsRef.current.push(fn);
        return () => {
          interceptorsRef.current = interceptorsRef.current.filter((f) => f !== fn);
        };
      },
    }),
    []
  );

  useEffect(() => {
    const wa = getWebApp();
    if (!wa) return;

    try {
      wa.ready();
      wa.expand();
    } catch {
      // older clients
    }
    try {
      wa.setHeaderColor?.("#050505");
      wa.setBackgroundColor?.("#050505");
    } catch {
      // unsupported version
    }
    try {
      wa.disableVerticalSwipes?.();
    } catch {
      // unsupported version
    }

    const u = wa.initDataUnsafe?.user;
    const user: TelegramUser | null =
      u && u.id
        ? {
            id: u.id,
            firstName: u.first_name ?? "Friend",
            lastName: u.last_name,
            username: u.username,
            photoUrl: u.photo_url,
          }
        : null;

    // Deferred a tick — synchronous setState inside an effect cascades
    // renders (react-hooks/set-state-in-effect).
    const timer = window.setTimeout(() => {
      setCtx({ available: Boolean(wa.initData), user });
    }, 0);
    return () => window.clearTimeout(timer);
  }, []);

  return (
    <TelegramContext.Provider value={ctx}>
      <BackInterceptorsContext.Provider value={backCtx}>{children}</BackInterceptorsContext.Provider>
    </TelegramContext.Provider>
  );
}

export function useTelegram(): TelegramCtx {
  return useContext(TelegramContext);
}

/* ---------------- haptic feedback ---------------- */

export const haptics = {
  /** Light tap — generic button presses. */
  tap(): void {
    try {
      getWebApp()?.HapticFeedback?.impactOccurred("light");
    } catch {
      // ignore
    }
  },
  /** Selection change — pickers, tabs, calendar days. */
  select(): void {
    try {
      getWebApp()?.HapticFeedback?.selectionChanged();
    } catch {
      // ignore
    }
  },
  /** Medium impact — selecting a table. */
  selectMedium(): void {
    try {
      getWebApp()?.HapticFeedback?.impactOccurred("medium");
    } catch {
      // ignore
    }
  },
  success(): void {
    try {
      getWebApp()?.HapticFeedback?.notificationOccurred("success");
    } catch {
      // ignore
    }
  },
  error(): void {
    try {
      getWebApp()?.HapticFeedback?.notificationOccurred("error");
    } catch {
      // ignore
    }
  },
  warning(): void {
    try {
      getWebApp()?.HapticFeedback?.notificationOccurred("warning");
    } catch {
      // ignore
    }
  },
};

/* ---------------- BackButton ---------------- */

/**
 * A back interceptor returns TRUE when it consumed the back action
 * (e.g. the reserve wizard stepping back one stage, or the nav sheet closing).
 * When every interceptor declines, the shell falls back to its default
 * navigation ("go home").
 */
export type BackInterceptor = () => boolean;

interface BackCtx {
  runInterceptors: () => boolean;
  registerBackInterceptor: (fn: BackInterceptor) => () => void;
}

const NoopBackCtx: BackCtx = {
  runInterceptors: () => false,
  registerBackInterceptor: () => () => undefined,
};

const BackInterceptorsContext = createContext<BackCtx>(NoopBackCtx);

export function useTelegramBackInterceptors(): BackCtx {
  return useContext(BackInterceptorsContext);
}

/**
 * Lets a nested screen handle Telegram's BackButton before the shell's
 * default ("go home"). Pass `null` to unregister / decline.
 */
export function useTelegramBackInterceptor(handler: BackInterceptor | null): void {
  const { registerBackInterceptor } = useTelegramBackInterceptors();
  const handlerRef = useRef(handler);
  const hasHandler = handler !== null;

  useEffect(() => {
    handlerRef.current = handler;
  });

  useEffect(() => {
    if (!hasHandler) return;
    return registerBackInterceptor(() => {
      const h = handlerRef.current;
      return h ? h() : false;
    });
  }, [hasHandler, registerBackInterceptor]);
}

/** Shows Telegram's BackButton while `active`, wiring it to `onBack`. */
export function useTelegramBackButton(active: boolean, onBack: () => void): void {
  const handlerRef = useRef(onBack);

  useEffect(() => {
    handlerRef.current = onBack;
  });

  useEffect(() => {
    const bb = getWebApp()?.BackButton;
    if (!bb || !active) return;
    const handler = () => handlerRef.current();
    bb.onClick(handler);
    bb.show();
    return () => {
      try {
        bb.offClick(handler);
        bb.hide();
      } catch {
        // ignore
      }
    };
  }, [active]);
}

/* ---------------- MainButton ---------------- */

/** Drives Telegram's MainButton from React state. */
export function useTelegramMainButton(opts: {
  text: string;
  onClick: () => void;
  visible: boolean;
  enabled?: boolean;
}): void {
  const { text, visible, enabled = true } = opts;
  const handlerRef = useRef(opts.onClick);

  useEffect(() => {
    handlerRef.current = opts.onClick;
  });

  useEffect(() => {
    const mb = getWebApp()?.MainButton;
    if (!mb) return;
    const handler = () => handlerRef.current();
    mb.onClick(handler);
    if (visible) {
      try {
        mb.setText(text);
        mb.setParams({ color: "#f5bd19", text_color: "#080808", is_active: enabled, is_visible: true });
      } catch {
        // ignore
      }
      mb.show();
      if (enabled) mb.enable();
      else mb.disable();
    } else {
      mb.hide();
    }
    return () => {
      try {
        mb.offClick(handler);
        mb.hide();
      } catch {
        // ignore
      }
    };
  }, [visible, text, enabled]);
}

/* ---------------- closing confirmation ---------------- */

/** Warns the user before closing the Mini App while `active`. */
export function useTelegramClosingConfirmation(active: boolean): void {
  useEffect(() => {
    const wa = getWebApp();
    if (!wa || !active) return;
    try {
      wa.enableClosingConfirmation?.();
    } catch {
      // ignore
    }
    return () => {
      try {
        wa.disableClosingConfirmation?.();
      } catch {
        // ignore
      }
    };
  }, [active]);
}

/* ---------------- CloudStorage ---------------- */

/** Telegram CloudStorage persists per user across devices & sessions
 *  (localStorage inside the webview can be evicted and is device-local).
 *  Available from Mini App version 6.4; all helpers resolve quietly
 *  (with null) when running outside Telegram or on old clients. */

/** Semver-ish compare: "6.0" >= "6.4" -> false, "7.2" >= "6.4" -> true. */
function versionAtLeast(version: string, min: string): boolean {
  const a = version.split(".").map((n) => Number(n) || 0);
  const b = min.split(".").map((n) => Number(n) || 0);
  for (let i = 0; i < 3; i++) {
    const x = a[i] ?? 0;
    const y = b[i] ?? 0;
    if (x !== y) return x > y;
  }
  return true;
}

function cloudStorageReady(): boolean {
  const wa = getWebApp();
  if (!wa?.CloudStorage) return false;
  // Old clients log an error and never fire the callback — check first.
  return versionAtLeast(wa.version ?? "0", "6.4");
}

export function cloudStorageSet(key: string, value: string): Promise<void> {
  return new Promise((resolve) => {
    if (!cloudStorageReady()) {
      resolve();
      return;
    }
    try {
      getWebApp()?.CloudStorage?.setItem(key, value, () => resolve());
      // Safety net: never leave the caller hanging on a stuck callback.
      window.setTimeout(resolve, 1000);
    } catch {
      resolve();
    }
  });
}

export function cloudStorageGet(key: string): Promise<string | null> {
  return new Promise((resolve) => {
    if (!cloudStorageReady()) {
      resolve(null);
      return;
    }
    let settled = false;
    const done = (value: string | null) => {
      if (!settled) {
        settled = true;
        resolve(value);
      }
    };
    try {
      getWebApp()?.CloudStorage?.getItem(key, (_err, value) =>
        done(typeof value === "string" && value !== "" ? value : null)
      );
      window.setTimeout(() => done(null), 1000);
    } catch {
      done(null);
    }
  });
}

export function cloudStorageRemove(key: string): Promise<void> {
  return new Promise((resolve) => {
    if (!cloudStorageReady()) {
      resolve();
      return;
    }
    try {
      getWebApp()?.CloudStorage?.removeItem(key, () => resolve());
      window.setTimeout(resolve, 1000);
    } catch {
      resolve();
    }
  });
}

/** Storage key for the saved guest profile (CloudStorage + localStorage). */
export const GB_DETAILS_KEY = "gb:details";

/* ---------------- link helpers ---------------- */

/** Opens a URL outside Telegram (browser) via the native handler when possible. */
export function openExternal(url: string): void {
  const wa = getWebApp();
  try {
    if (wa && wa.initData) wa.openLink(url);
    else window.open(url, "_blank", "noopener,noreferrer");
  } catch {
    window.open(url, "_blank", "noopener,noreferrer");
  }
}

/** Shares `text`/`url` through Telegram's native share dialog when inside Telegram. */
export function shareViaTelegram(text: string, url?: string): boolean {
  const wa = getWebApp();
  if (!wa || !wa.initData) return false;
  const shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url ?? "")}&text=${encodeURIComponent(text)}`;
  try {
    wa.openTelegramLink(shareUrl);
    return true;
  } catch {
    return false;
  }
}
