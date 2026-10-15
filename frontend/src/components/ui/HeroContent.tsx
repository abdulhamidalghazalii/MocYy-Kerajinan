"use client";

import React from "react";
import { ArrowRight, ShoppingBag, Eye, Award, Truck, HeartHandshake } from "lucide-react";

interface HeroContentProps {
  onShopNow: () => void;
  onExploreCollection: () => void;
}

export default function HeroContent({
  onShopNow,
  onExploreCollection,
}: HeroContentProps) {
  return (
    <div className="flex flex-col justify-center max-w-2xl py-6 lg:py-16 z-20">
      {/* Handcrafted Badge */}
      <div className="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-rose-100/80 border border-rose-200/60 text-[#782046] text-xs font-semibold tracking-wide w-fit mb-6 shadow-xs animate-fade-in">
        <span className="w-2 h-2 rounded-full bg-[#782046] animate-pulse" />
        <span>MocYy • Koleksi Eksklusif Florist & Hadiah</span>
      </div>

      {/* Main Title */}
      <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-stone-900 tracking-tight leading-[1.12] font-serif">
        Sentuhan Keanggunan Abadi Dalam Setiap{" "}
        <span className="rose-gradient-text inline-block">Bucket Bunga</span>
      </h1>

      {/* Short Description */}
      <p className="mt-5 text-base sm:text-lg text-stone-600 leading-relaxed max-w-xl font-normal">
        Hadiahkan momen tak terlupakan untuk wisuda, ulang tahun, dan anniversary.
        Nikmati pengalaman visualisasi 3D interaktif sebelum memilih bucket impian Anda.
      </p>

      {/* Action Buttons: Belanja Sekarang & Jelajahi Koleksi */}
      <div className="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
        <button
          onClick={onShopNow}
          id="hero-shop-now-btn"
          className="group px-8 py-4 rounded-2xl bg-[#782046] hover:bg-[#621837] text-white font-semibold text-base shadow-xl shadow-rose-900/25 active:scale-[0.98] transition-all flex items-center justify-center gap-3"
        >
          <ShoppingBag className="w-5 h-5 text-rose-200" />
          <span>Belanja Sekarang</span>
          <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </button>

        <button
          onClick={onExploreCollection}
          id="hero-explore-collection-btn"
          className="px-8 py-4 rounded-2xl bg-white/90 hover:bg-white text-stone-800 font-semibold text-base border border-stone-200/90 shadow-md hover:shadow-lg active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 backdrop-blur-md"
        >
          <Eye className="w-5 h-5 text-stone-600" />
          <span>Jelajahi Koleksi</span>
        </button>
      </div>

      {/* Key Value Guarantees */}
      <div className="mt-12 pt-8 border-t border-stone-200/70 grid grid-cols-3 gap-4 text-left">
        <div className="flex flex-col gap-1">
          <div className="flex items-center gap-1.5 text-[#782046]">
            <Award className="w-4 h-4" />
            <span className="text-xs font-bold uppercase tracking-wider text-stone-800">100% Segar</span>
          </div>
          <p className="text-[11px] text-stone-500 leading-tight">Bunga premium kualitas florist terpilih</p>
        </div>

        <div className="flex flex-col gap-1">
          <div className="flex items-center gap-1.5 text-emerald-600">
            <Truck className="w-4 h-4" />
            <span className="text-xs font-bold uppercase tracking-wider text-stone-800">Same-Day</span>
          </div>
          <p className="text-[11px] text-stone-500 leading-tight">Pengiriman instan aman tiba hari ini</p>
        </div>

        <div className="flex flex-col gap-1">
          <div className="flex items-center gap-1.5 text-amber-600">
            <HeartHandshake className="w-4 h-4" />
            <span className="text-xs font-bold uppercase tracking-wider text-stone-800">Kartu Gratis</span>
          </div>
          <p className="text-[11px] text-stone-500 leading-tight">Pesan custom personal dicetak elegan</p>
        </div>
      </div>
    </div>
  );
}
