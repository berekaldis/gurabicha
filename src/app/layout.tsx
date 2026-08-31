import type { Metadata, Viewport } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Gura Bicha Sports Bar",
  description:
    "Live music. Great company. The game on. Reserve your table for Game & Groove — Acoustic Thursdays at Gura Bicha Sports Bar, Addis Ababa.",
  keywords: ["Gura Bicha", "Sports Bar", "Addis Ababa", "Acoustic Thursdays", "Table Reservation"],
  icons: {
    icon: "/logo.svg",
  },
  openGraph: {
    title: "Gura Bicha Sports Bar",
    description: "Game & Groove — Acoustic Thursdays. Reserve your table.",
    siteName: "Gura Bicha Sports Bar",
    type: "website",
  },
};

export const viewport: Viewport = {
  themeColor: "#050505",
  width: "device-width",
  initialScale: 1,
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="dark" suppressHydrationWarning>
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
        <link
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap"
          rel="stylesheet"
        />
      </head>
      <body className="antialiased bg-background text-foreground">{children}</body>
    </html>
  );
}
