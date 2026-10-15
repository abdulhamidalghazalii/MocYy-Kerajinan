"use client";

import React from "react";
import Image from "next/image";
import { CartItem } from "@/types/ecommerce";
import { X, Trash2, Plus, Minus, ArrowRight, ShoppingBag, Gift } from "lucide-react";

interface CartDrawerProps {
  isOpen: boolean;
  onClose: () => void;
  cartItems: CartItem[];
  onUpdateQuantity: (index: number, quantity: number) => void;
  onRemoveItem: (index: number) => void;
  onClearCart: () => void;
  onCheckout: () => void;
}

export default function CartDrawer({
  isOpen,
  onClose,
  cartItems,
  onUpdateQuantity,
  onRemoveItem,
  onClearCart,
  onCheckout,
}: CartDrawerProps) {
  if (!isOpen) return null;

  const subtotal = cartItems.reduce((acc, item) => {
    const unitPrice = item.variant
      ? item.product.effective_price + item.variant.additional_price
      : item.product.effective_price;
    return acc + unitPrice * item.quantity;
  }, 0);

  return (
    <div className="fixed inset-0 z-50 overflow-hidden">
      {/* Backdrop */}
      <div
        onClick={onClose}
        className="absolute inset-0 bg-stone-900/40 backdrop-blur-xs transition-opacity duration-300"
      />

      <div className="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div className="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">
          {/* Header */}
          <div className="p-6 border-b border-stone-200 flex items-center justify-between">
            <div className="flex items-center gap-2">
              <ShoppingBag className="w-5 h-5 text-[#782046]" />
              <h2 className="text-lg font-bold text-stone-900 font-serif">Keranjang Belanja</h2>
              <span className="text-xs px-2 py-0.5 rounded-full bg-rose-100 text-[#782046] font-bold">
                {cartItems.length}
              </span>
            </div>
            <button
              onClick={onClose}
              className="p-2 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Cart Item List */}
          <div className="flex-1 overflow-y-auto p-6 space-y-4">
            {cartItems.length === 0 ? (
              <div className="h-full flex flex-col items-center justify-center text-center p-6 text-stone-400">
                <div className="w-16 h-16 rounded-full bg-rose-50 flex items-center justify-center mb-4">
                  <ShoppingBag className="w-8 h-8 text-rose-300" />
                </div>
                <h3 className="text-base font-bold text-stone-700">Keranjang Masih Kosong</h3>
                <p className="text-xs text-stone-500 mt-1 max-w-xs">
                  Temukan rangkaian bucket bunga elegan atau hampers spesial untuk melengkapi momen Anda.
                </p>
                <button
                  onClick={onClose}
                  className="mt-6 px-6 py-2.5 rounded-full bg-[#782046] text-white text-xs font-semibold shadow-md hover:bg-[#621837] transition"
                >
                  Mulai Belanja
                </button>
              </div>
            ) : (
              <>
                <div className="flex items-center justify-between text-xs text-stone-500 pb-2 border-b border-stone-100">
                  <span>Daftar Pesanan ({cartItems.length} item)</span>
                  <button
                    onClick={onClearCart}
                    className="text-stone-400 hover:text-rose-600 transition flex items-center gap-1"
                  >
                    <Trash2 className="w-3.5 h-3.5" />
                    <span>Kosongkan</span>
                  </button>
                </div>

                {cartItems.map((item, idx) => {
                  const unitPrice = item.variant
                    ? item.product.effective_price + item.variant.additional_price
                    : item.product.effective_price;
                  const lineTotal = unitPrice * item.quantity;

                  return (
                    <div
                      key={`${item.product.id}-${item.variant?.id || "default"}-${idx}`}
                      className="p-3.5 rounded-2xl bg-stone-50/70 border border-stone-200/80 flex gap-3 items-center"
                    >
                      <div className="relative w-16 h-16 rounded-xl overflow-hidden bg-white shrink-0 border border-stone-200">
                        <Image
                          src={item.product.main_image}
                          alt={item.product.name}
                          fill
                          className="object-cover"
                        />
                      </div>

                      <div className="flex-1 min-w-0">
                        <h4 className="text-xs font-bold text-stone-900 truncate">
                          {item.product.name}
                        </h4>
                        {item.variant && (
                          <p className="text-[11px] text-stone-500 truncate">
                            {item.variant.variant_name}: {item.variant.variant_value}
                          </p>
                        )}
                        <div className="text-xs font-extrabold text-[#782046] mt-1">
                          Rp {lineTotal.toLocaleString("id-ID")}
                        </div>
                      </div>

                      <div className="flex flex-col items-end gap-2 shrink-0">
                        <button
                          onClick={() => onRemoveItem(idx)}
                          className="text-stone-400 hover:text-rose-600 p-1"
                        >
                          <X className="w-3.5 h-3.5" />
                        </button>

                        <div className="flex items-center border border-stone-300 rounded-lg bg-white overflow-hidden text-xs">
                          <button
                            onClick={() => onUpdateQuantity(idx, item.quantity - 1)}
                            className="p-1 hover:bg-stone-100 text-stone-600"
                          >
                            <Minus className="w-3 h-3" />
                          </button>
                          <span className="px-2 font-semibold text-stone-800">
                            {item.quantity}
                          </span>
                          <button
                            onClick={() => onUpdateQuantity(idx, item.quantity + 1)}
                            className="p-1 hover:bg-stone-100 text-stone-600"
                          >
                            <Plus className="w-3 h-3" />
                          </button>
                        </div>
                      </div>
                    </div>
                  );
                })}

                <div className="p-3 rounded-2xl bg-amber-50/80 border border-amber-200/60 text-xs text-amber-900 flex items-start gap-2.5">
                  <Gift className="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                  <div>
                    <span className="font-bold">Gratis Kartu Ucapan Eksklusif!</span>
                    <p className="text-[11px] text-amber-800 mt-0.5">
                      Anda dapat menuliskan ucapan personal pada formulir checkout berikutnya.
                    </p>
                  </div>
                </div>
              </>
            )}
          </div>

          {/* Footer & Checkout Button */}
          {cartItems.length > 0 && (
            <div className="p-6 border-t border-stone-200 bg-stone-50/80 space-y-4">
              <div className="space-y-1.5 text-xs text-stone-600">
                <div className="flex justify-between">
                  <span>Subtotal Produk</span>
                  <span className="font-semibold text-stone-900">
                    Rp {subtotal.toLocaleString("id-ID")}
                  </span>
                </div>
                <div className="flex justify-between text-emerald-600 font-medium">
                  <span>Biaya Pengemasan Mewah</span>
                  <span>GRATIS</span>
                </div>
                <div className="flex justify-between text-sm font-bold text-stone-900 pt-2 border-t border-stone-200">
                  <span>Estimasi Total</span>
                  <span className="text-base text-[#782046]">
                    Rp {subtotal.toLocaleString("id-ID")}
                  </span>
                </div>
              </div>

              <button
                onClick={onCheckout}
                className="w-full py-4 rounded-2xl bg-[#782046] hover:bg-[#621837] text-white font-semibold text-sm shadow-xl shadow-rose-900/20 active:scale-[0.98] transition flex items-center justify-center gap-2"
              >
                <span>Lanjut ke Pembayaran</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
