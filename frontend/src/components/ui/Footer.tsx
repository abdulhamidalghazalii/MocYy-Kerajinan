"use client";

import React from "react";
import { Sparkles, Phone, Mail, MapPin, Clock, ShieldCheck, Heart } from "lucide-react";
import { STORE_CONFIG } from "@/config/store";

export default function Footer() {
  return (
    <footer className="w-full bg-[#1c1917] text-stone-300 border-t border-stone-800 pt-16 pb-12 mt-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-stone-800">
          {/* Brand Info */}
          <div className="space-y-4">
            <div className="flex items-center gap-2.5">
              <div className="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#782046] to-[#c94d75] flex items-center justify-center text-white font-serif font-bold text-base">
                M
              </div>
              <span className="text-2xl font-bold tracking-tight text-white font-serif">
                {STORE_CONFIG.name}
              </span>
            </div>
            <p className="text-xs text-stone-400 leading-relaxed">
              Boutique florist terpercaya menghadirkan rangkaian bucket bunga segar, artificial,
              money bouquet, dan hampers hadiah eksklusif dengan teknologi visualisasi 3D tercanggih.
            </p>
            <div className="flex items-center gap-3 pt-2 text-stone-400">
              <a
                href={`https://instagram.com/${STORE_CONFIG.instagram.replace('@', '')}`}
                target="_blank"
                rel="noreferrer"
                className="w-8 h-8 rounded-full bg-stone-800 hover:bg-[#782046] hover:text-white flex items-center justify-center transition"
                title="Instagram"
              >
                <svg className="w-4 h-4 fill-currentColor" viewBox="0 0 24 24">
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                </svg>
              </a>
              <a
                href={STORE_CONFIG.getWhatsAppLink("Halo MocYy, saya ingin tanya seputar pemesanan buket.")}
                target="_blank"
                rel="noreferrer"
                className="w-8 h-8 rounded-full bg-stone-800 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"
                title={`WhatsApp: ${STORE_CONFIG.phoneDisplay}`}
              >
                <Phone className="w-4 h-4" />
              </a>
            </div>
          </div>

          {/* Contact Details */}
          <div className="space-y-3">
            <h4 className="text-sm font-bold text-white uppercase tracking-wider font-serif">
              Hubungi Kami
            </h4>
            <div className="space-y-2.5 text-xs text-stone-400">
              <div className="flex items-start gap-2">
                <MapPin className="w-4 h-4 text-rose-400 shrink-0 mt-0.5" />
                <span>{STORE_CONFIG.address}</span>
              </div>
              <div className="flex items-center gap-2">
                <Phone className="w-4 h-4 text-rose-400 shrink-0" />
                <a href={STORE_CONFIG.whatsappUrl} target="_blank" rel="noreferrer" className="hover:text-rose-300 transition">
                  {STORE_CONFIG.phoneDisplay}
                </a>
              </div>
              <div className="flex items-center gap-2">
                <Mail className="w-4 h-4 text-rose-400 shrink-0" />
                <a href={`mailto:${STORE_CONFIG.email}`} className="hover:text-rose-300 transition">
                  {STORE_CONFIG.email}
                </a>
              </div>
              <div className="flex items-center gap-2">
                <Clock className="w-4 h-4 text-rose-400 shrink-0" />
                <span>{STORE_CONFIG.workingHours}</span>
              </div>
            </div>
          </div>

          {/* Popular Collections */}
          <div className="space-y-3">
            <h4 className="text-sm font-bold text-white uppercase tracking-wider font-serif">
              Koleksi Populer
            </h4>
            <ul className="space-y-2 text-xs text-stone-400">
              <li>
                <a href="#koleksi" className="hover:text-rose-400 transition">
                  Dusty Rose Romance Bouquet
                </a>
              </li>
              <li>
                <a href="#koleksi" className="hover:text-rose-400 transition">
                  Grand Royal 100K Money Bouquet
                </a>
              </li>
              <li>
                <a href="#koleksi" className="hover:text-rose-400 transition">
                  Golden Sweet Ferrero Chocolate
                </a>
              </li>
              <li>
                <a href="#koleksi" className="hover:text-rose-400 transition">
                  Scholar Teddy Graduation Kado
                </a>
              </li>
              <li>
                <a href="#koleksi" className="hover:text-rose-400 transition">
                  Preserved Rose Dome Abadi
                </a>
              </li>
            </ul>
          </div>

          {/* Security & Guarantees */}
          <div className="space-y-3">
            <h4 className="text-sm font-bold text-white uppercase tracking-wider font-serif">
              Jaminan Layanan
            </h4>
            <div className="p-4 rounded-2xl bg-stone-900 border border-stone-800 space-y-2.5 text-xs text-stone-400">
              <div className="flex items-center gap-2 text-emerald-400 font-semibold">
                <ShieldCheck className="w-4 h-4" />
                <span>Garansi Bunga Segar 100%</span>
              </div>
              <p className="text-[11px] leading-relaxed text-stone-400">
                Setiap rangkaian dirakit langsung sebelum pengiriman instan untuk menjaga kesegaran bunga secara maksimal.
              </p>
              <div className="pt-2 border-t border-stone-800 flex items-center justify-between text-[11px] text-stone-500">
                <span>QRIS Certified</span>
                <span>Instant GoSend / Grab</span>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom copyright */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
          <p>© {new Date().getFullYear()} {STORE_CONFIG.name}. Seluruh hak cipta dilindungi.</p>
          <div className="flex items-center gap-1 text-stone-400">
            <span>Dibuat dengan</span>
            <Heart className="w-3.5 h-3.5 text-rose-500 fill-rose-500" />
            <span>untuk Momen Spesial Anda</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
