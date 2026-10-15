import type { Metadata, Viewport } from "next";
import { Plus_Jakarta_Sans } from "next/font/google";
import "./globals.css";

const jakartaSans = Plus_Jakarta_Sans({
  subsets: ["latin"],
  display: "swap",
  variable: "--font-sans",
});

export const viewport: Viewport = {
  themeColor: "#9f496e",
  width: "device-width",
  initialScale: 1,
  maximumScale: 5,
};

export const metadata: Metadata = {
  title: "MocYy | Bucket Bunga Premium & Hadiah Mewah Interaktif 3D",
  description:
    "MocYy – Toko florist & kado terpercaya menghadirkan bucket bunga segar, artificial bouquet, money bouquet, dan hampers premium dengan pengalaman belanja interaktif 3D tercepat dan aman.",
  keywords: [
    "MocYy",
    "bucket bunga 3D",
    "florist jakarta",
    "toko bunga online",
    "money bouquet",
    "hampers wisuda",
    "hadiah anniversary",
  ],
  authors: [{ name: "MocYy" }],
  openGraph: {
    title: "MocYy | Luxury Flowers & Handcrafted Gifts",
    description: "Nikmati pengalaman memilih bucket bunga dengan objek 3D interaktif yang elegan dari MocYy. Pengiriman instan aman.",
    type: "website",
    locale: "id_ID",
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="id" className={`${jakartaSans.variable} scroll-smooth`}>
      <body className="min-h-screen flex flex-col bg-[#faf7f5] text-[#1c1917] antialiased selection:bg-[#f3d0dd] selection:text-[#782046]">
        {children}
      </body>
    </html>
  );
}
