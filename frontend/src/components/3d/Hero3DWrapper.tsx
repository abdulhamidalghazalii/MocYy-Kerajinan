"use client";

import React from "react";
import dynamic from "next/dynamic";
import Image from "next/image";

// Dynamic import with ssr: false to isolate Three.js from initial document bundle
const DynamicThreeHero = dynamic(() => import("./ThreeBouquetHero"), {
  ssr: false,
  loading: () => <HeroPlaceholder />,
});

function HeroPlaceholder() {
  return (
    <div className="relative w-full h-[450px] sm:h-[560px] lg:h-[640px] flex items-center justify-center p-4">
      <div className="relative w-full max-w-md h-full rounded-3xl overflow-hidden shadow-2xl border border-white/50 bg-gradient-to-br from-rose-50/70 via-white to-pink-50/40 flex items-center justify-center">
        {/* Instant visual fallback using prioritized WebP image to ensure stellar LCP */}
        <Image
          src="https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=1000&q=85"
          alt="MocYy Kerajinan Premium Flower Bouquet"
          fill
          priority
          sizes="(max-width: 768px) 100vw, 50vw"
          className="object-cover opacity-90 transition-opacity duration-500"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-stone-900/40 via-transparent to-transparent" />
        <div className="absolute bottom-5 left-5 right-5 p-3 rounded-2xl glass-panel text-xs text-stone-700 text-center font-medium shadow-md flex items-center justify-center gap-2">
          <div className="w-3.5 h-3.5 rounded-full border-2 border-rose-500 border-t-transparent animate-spin" />
          <span>Menyiapkan Engine 3D Interaktif...</span>
        </div>
      </div>
    </div>
  );
}

interface Hero3DWrapperProps {
  bouquetColor?: string;
  wrapperColor?: string;
  ribbonColor?: string;
}

export default function Hero3DWrapper(props: Hero3DWrapperProps) {
  return (
    <div className="w-full h-full relative">
      <DynamicThreeHero {...props} />
    </div>
  );
}
