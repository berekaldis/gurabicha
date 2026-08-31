"use client";

// Shared presentational primitives for the Gura Bicha design language
// (black & gold, Oswald display headings, pill buttons).

import { Loader2 } from "lucide-react";
import type { ReactNode } from "react";

export function GbPrimaryButton({
  children,
  onClick,
  disabled,
  loading,
  type = "button",
  className = "",
}: {
  children: ReactNode;
  onClick?: () => void;
  disabled?: boolean;
  loading?: boolean;
  type?: "button" | "submit";
  className?: string;
}) {
  return (
    <button
      type={type}
      onClick={onClick}
      disabled={disabled || loading}
      className={`flex w-full items-center justify-center gap-2 rounded-[10px] bg-gradient-to-br from-gold-light to-gold py-4 text-[13px] font-extrabold tracking-[0.05em] text-[#080808] transition hover:brightness-110 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:brightness-100 ${className}`}
    >
      {loading && <Loader2 className="h-4 w-4 animate-spin" aria-hidden />}
      {children}
    </button>
  );
}

export function GbSecondaryButton({
  children,
  onClick,
  disabled,
  className = "",
}: {
  children: ReactNode;
  onClick?: () => void;
  disabled?: boolean;
  className?: string;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled}
      className={`w-full rounded-[10px] border border-gold/30 py-3.5 text-[13px] font-bold tracking-[0.05em] text-gold-light transition hover:bg-gold/10 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-40 ${className}`}
    >
      {children}
    </button>
  );
}

export function GbGhostButton({
  children,
  onClick,
  className = "",
}: {
  children: ReactNode;
  onClick?: () => void;
  className?: string;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      className={`w-full py-3 text-[12px] font-semibold tracking-wide text-white/50 transition hover:text-white ${className}`}
    >
      {children}
    </button>
  );
}

export function GbPill({ children, className = "" }: { children: ReactNode; className?: string }) {
  return (
    <span
      className={`inline-flex items-center gap-1.5 rounded-full border border-gold/30 bg-black/50 px-3 py-1.5 text-[11px] font-semibold text-gold-light ${className}`}
    >
      {children}
    </span>
  );
}

export function GbSectionHeader({
  title,
  linkLabel,
  onLink,
}: {
  title: string;
  linkLabel?: string;
  onLink?: () => void;
}) {
  return (
    <div className="mb-3.5 flex items-center justify-between">
      <h2 className="font-display text-xl uppercase tracking-wide text-white">{title}</h2>
      {linkLabel && onLink && (
        <button
          type="button"
          onClick={onLink}
          className="text-[11px] font-bold tracking-wider text-gold transition hover:text-gold-light"
        >
          {linkLabel}
        </button>
      )}
    </div>
  );
}

export function GbInfoCard({ label, value, sub }: { label: string; value: ReactNode; sub?: ReactNode }) {
  return (
    <div className="mb-4 rounded-xl border border-gold/25 bg-panel p-3.5">
      <small className="text-[10px] uppercase tracking-wider text-white/45">{label}</small>
      <strong className="mt-1 block font-display text-xl leading-tight text-gold">{value}</strong>
      {sub && <small className="mt-0.5 block text-[11px] text-white/50">{sub}</small>}
    </div>
  );
}

export function GbSkeleton({ className = "" }: { className?: string }) {
  return <div className={`animate-pulse rounded-xl bg-white/[0.06] ${className}`} />;
}

export function GbErrorBox({ message, onRetry }: { message: string; onRetry?: () => void }) {
  return (
    <div className="rounded-xl border border-red-500/25 bg-red-500/[0.06] p-4 text-center">
      <p className="text-[12px] text-red-300">{message}</p>
      {onRetry && (
        <button
          type="button"
          onClick={onRetry}
          className="mt-2 text-[11px] font-bold tracking-wider text-gold hover:text-gold-light"
        >
          TRY AGAIN
        </button>
      )}
    </div>
  );
}
