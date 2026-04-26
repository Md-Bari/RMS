import type { Metadata } from "next";
import { Manrope, Playfair_Display } from "next/font/google";
import type { ReactNode } from "react";
import "./globals.css";
import { CartProvider } from "@/lib/store/cart-store";
import { cn } from "@/lib/utils";
import { ToastRegion } from "@/components/ui/toast-region";

const manrope = Manrope({
  subsets: ["latin"],
  variable: "--font-sans"
});

const playfair = Playfair_Display({
  subsets: ["latin"],
  variable: "--font-display"
});

export const metadata: Metadata = {
  title: "Savoria Table",
  description: "Modern restaurant QR ordering SaaS built with Next.js 14, TypeScript, Tailwind, and frontend-only local state."
};

export default function RootLayout({
  children
}: Readonly<{
  children: ReactNode;
}>) {
  return (
    <html lang="en">
      <body className={cn(manrope.variable, playfair.variable, "font-sans antialiased")}>
        <CartProvider>
          <ToastRegion />
          {children}
        </CartProvider>
      </body>
    </html>
  );
}
