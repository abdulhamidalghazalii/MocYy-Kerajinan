"use client";

import React, { useState } from "react";
import { ShoppingBag, Search, Sparkles, MessageCircle, Menu, X } from "lucide-react";
import { STORE_CONFIG } from "@/config/store";

interface NavbarProps {
  cartCount: number;
  onOpenCart: () => void;
  onOpenTracking: () => void;
  onSearch?: (query: string) => void;
}

export default function Navbar({
  cartCount,
  onOpenCart,
  onOpenTracking,
  onSearch,
}: NavbarProps) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [searchVal, setSearchVal] = useState("");

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (onSearch) onSearch(searchVal);
  };

  return (
    <header className="sticky top-0 z-40 w-full backdrop-blur-xl bg-white/80 border-b border-stone-200/60 transition-all duration-300">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
        {/* Brand Logo - MocYy Typography Logo */}
        <a href="#hero" className="flex items-center gap-3 group">
          <div className="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#782046] to-[#c94d75] flex items-center justify-center text-white shadow-md shadow-rose-900/10 group-hover:scale-105 transition-transform">
            <span className="font-serif font-bold text-lg text-amber-100">M</span>
          </div>
          <div className="flex flex-col">
            <span className="text-2xl font-bold tracking-tight text-stone-900 group-hover:text-[#782046] transition-colors font-serif">
              {STORE_CONFIG.name}
            </span>
            <span className="text-[10px] uppercase tracking-widest text-[#782046]/80 font-semibold -mt-1">
              {STORE_CONFIG.tagline}
            </span>
          </div>
        </a>

        {/* Desktop Navigation Links */}
        <nav className="hidden md:flex items-center gap-8 text-sm font-medium text-stone-600">
          <a href="#koleksi" className="hover:text-[#782046] transition-colors">
            Katalog Produk
          </a>
          <a href="#showcase-3d" className="hover:text-[#782046] transition-colors flex items-center gap-1.5">
            <span className="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse" />
            <span>Showcase 3D</span>
          </a>
          <a href="#keunggulan" className="hover:text-[#782046] transition-colors">
            Tentang Kami
          </a>
          <button
            onClick={onOpenTracking}
            className="hover:text-[#782046] transition-colors font-medium text-left"
          >
            Lacak Pesanan
          </button>
        </nav>

        {/* Right Actions: Search, WhatsApp, Cart */}
        <div className="flex items-center gap-3">
          {/* Quick Search */}
          <form onSubmit={handleSearchSubmit} className="hidden lg:flex items-center relative">
            <input
              type="text"
              placeholder="Cari mawar, money bouquet..."
              value={searchVal}
              onChange={(e) => setSearchVal(e.target.value)}
              className="w-56 pl-9 pr-3 py-2 rounded-full text-xs bg-stone-100/80 border border-stone-200/80 focus:outline-hidden focus:ring-2 focus:ring-[#782046]/20 focus:bg-white transition-all text-stone-800 placeholder-stone-400"
            />
            <Search className="w-3.5 h-3.5 text-stone-400 absolute left-3 pointer-events-none" />
          </form>

          {/* WhatsApp Direct */}
          <a
            href={STORE_CONFIG.getWhatsAppLink("Halo MocYy, saya ingin konsultasi pemesanan buket bunga & kado hadiah.")}
            target="_blank"
            rel="noopener noreferrer"
            className="hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition"
          >
            <MessageCircle className="w-3.5 h-3.5 text-emerald-600" />
            <span>Tanya Florist</span>
          </a>

          {/* Cart Trigger */}
          <button
            onClick={onOpenCart}
            aria-label="Buka Keranjang Belanja"
            className="relative p-2.5 rounded-2xl bg-stone-100 hover:bg-stone-200/80 text-stone-800 transition active:scale-95"
          >
            <ShoppingBag className="w-5 h-5 text-stone-800" />
            {cartCount > 0 && (
              <span className="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-[#782046] text-white text-[11px] font-bold flex items-center justify-center shadow-md animate-bounce">
                {cartCount}
              </span>
            )}
          </button>

          {/* Mobile Menu Toggle */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden p-2 rounded-xl text-stone-700 hover:bg-stone-100"
            aria-label="Toggle menu"
          >
            {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </div>

      {/* Mobile Drawer */}
      {mobileMenuOpen && (
        <div className="md:hidden px-4 pt-2 pb-6 bg-white/95 backdrop-blur-xl border-b border-stone-200 space-y-3">
          <form onSubmit={handleSearchSubmit} className="relative w-full">
            <input
              type="text"
              placeholder="Cari bucket bunga atau kado..."
              value={searchVal}
              onChange={(e) => setSearchVal(e.target.value)}
              className="w-full pl-9 pr-3 py-2.5 rounded-xl text-xs bg-stone-100 border border-stone-200 text-stone-800"
            />
            <Search className="w-4 h-4 text-stone-400 absolute left-3 top-3" />
          </form>

          <div className="flex flex-col space-y-2 pt-2 text-sm font-medium text-stone-700">
            <a
              href="#koleksi"
              onClick={() => setMobileMenuOpen(false)}
              className="p-2.5 rounded-lg hover:bg-stone-100"
            >
              Katalog Produk
            </a>
            <a
              href="#showcase-3d"
              onClick={() => setMobileMenuOpen(false)}
              className="p-2.5 rounded-lg hover:bg-stone-100 flex items-center justify-between"
            >
              <span>Showcase 3D</span>
              <span className="text-[10px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold">
                Interaktif
              </span>
            </a>
            <a
              href="#keunggulan"
              onClick={() => setMobileMenuOpen(false)}
              className="p-2.5 rounded-lg hover:bg-stone-100"
            >
              Tentang Kami & Keunggulan
            </a>
            <button
              onClick={() => {
                setMobileMenuOpen(false);
                onOpenTracking();
              }}
              className="p-2.5 rounded-lg hover:bg-stone-100 text-left font-medium"
            >
              Lacak Pesanan
            </button>
            <a
              href={STORE_CONFIG.getWhatsAppLink("Halo MocYy, saya ingin info pemesanan buket.")}
              target="_blank"
              rel="noopener noreferrer"
              className="p-2.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold flex items-center gap-2"
            >
              <MessageCircle className="w-4 h-4" />
              <span>WhatsApp Official ({STORE_CONFIG.phoneDisplay})</span>
            </a>
          </div>
        </div>
      )}
    </header>
  );
}
