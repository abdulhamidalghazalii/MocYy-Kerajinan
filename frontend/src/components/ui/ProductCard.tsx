"use client";

import React from "react";
import Image from "next/image";
import { Product } from "@/types/ecommerce";
import { Box, Plus, Check } from "lucide-react";

interface ProductCardProps {
  product: Product;
  onInspect3D: (product: Product) => void;
  onAddToCart: (product: Product) => void;
  isAdded?: boolean;
}

export default function ProductCard({
  product,
  onInspect3D,
  onAddToCart,
  isAdded = false,
}: ProductCardProps) {
  return (
    <div className="group relative bg-white rounded-3xl p-4 border border-stone-200/80 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between hover:-translate-y-1">
      <div>
        {/* Product Image Frame */}
        <div className="relative w-full aspect-square rounded-2xl overflow-hidden bg-stone-100 mb-4">
          <Image
            src={product.main_image}
            alt={product.name}
            fill
            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
            loading="lazy"
            className="object-cover group-hover:scale-105 transition-transform duration-500"
          />

          {/* Badges */}
          <div className="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
            {product.has_discount && (
              <span className="px-2.5 py-1 rounded-full bg-[#782046] text-white text-[11px] font-bold shadow-sm">
                -{product.discount_percentage}%
              </span>
            )}
            {product.is_featured && (
              <span className="px-2.5 py-1 rounded-full bg-amber-500 text-white text-[11px] font-bold shadow-sm">
                Best Seller
              </span>
            )}
          </div>

          {/* 3D Inspect Button Overlay */}
          <button
            onClick={() => onInspect3D(product)}
            className="absolute bottom-3 right-3 px-3 py-1.5 rounded-xl glass-panel text-xs font-semibold text-stone-800 shadow-md hover:bg-white flex items-center gap-1.5 transition active:scale-95 z-10"
            title="Lihat dalam 3D Interaktif"
          >
            <Box className="w-3.5 h-3.5 text-[#782046]" />
            <span>Mode 3D</span>
          </button>
        </div>

        {/* Product Meta */}
        <div className="px-1">
          <span className="text-[11px] font-semibold uppercase tracking-wider text-stone-400">
            {product.category?.name || "Bucket Bunga"}
          </span>
          <h3 className="text-base font-bold text-stone-900 line-clamp-1 mt-0.5 group-hover:text-[#782046] transition-colors">
            {product.name}
          </h3>
          <p className="text-xs text-stone-500 line-clamp-2 mt-1 leading-relaxed">
            {product.description}
          </p>
        </div>
      </div>

      {/* Pricing & Add to Cart */}
      <div className="mt-4 pt-3 border-t border-stone-100 flex items-center justify-between gap-2 px-1">
        <div>
          <div className="text-base font-extrabold text-[#782046]">
            Rp {product.effective_price.toLocaleString("id-ID")}
          </div>
          {product.has_discount && (
            <div className="text-[11px] text-stone-400 line-through">
              Rp {product.price.toLocaleString("id-ID")}
            </div>
          )}
        </div>

        <button
          onClick={() => onAddToCart(product)}
          className={`p-2.5 rounded-xl font-semibold text-xs transition-all active:scale-95 flex items-center gap-1 ${
            isAdded
              ? "bg-emerald-600 text-white shadow-xs"
              : "bg-stone-900 hover:bg-[#782046] text-white shadow-xs"
          }`}
          title="Tambah ke Keranjang Belanja"
        >
          {isAdded ? (
            <>
              <Check className="w-4 h-4" />
              <span className="hidden sm:inline">Masuk</span>
            </>
          ) : (
            <>
              <Plus className="w-4 h-4" />
              <span className="hidden sm:inline">Beli</span>
            </>
          )}
        </button>
      </div>
    </div>
  );
}
