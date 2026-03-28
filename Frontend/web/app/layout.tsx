import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import { Toaster } from "react-hot-toast";
import { dir } from 'i18next'
import { I18nProvider } from "@/components/i18n";
// import { languages } from "@/i18n/setttings";
import { NextIntlClientProvider } from 'next-intl';

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: "SIPRO",
  description: "Sistem Informasi Properti",
  icons: {
    icon: "/logo-only.png", // bisa juga .png atau .svg
    shortcut: "/assets/logo-only.png",
    apple: "/assets/logo-only.png",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html data-theme="light">
      <body
        className={`${geistSans.variable} ${geistMono.variable} antialiased`}
      >
        <NextIntlClientProvider>
          {children}
        </NextIntlClientProvider>
        
        <Toaster position="top-center" />
      </body>
    </html>
  );
}
