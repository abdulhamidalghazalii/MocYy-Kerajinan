"use client";

import React, { useState } from "react";
import { trackOrder } from "@/services/api";
import { X, Search, PackageCheck, AlertCircle, Clock, MapPin, Truck } from "lucide-react";

interface OrderTrackingModalProps {
  isOpen: boolean;
  onClose: () => void;
}

interface TrackedOrder {
  order_number: string;
  created_at: string;
  order_status: string;
  payment_status: string;
  recipient_name: string;
  shipping_address: string;
  city: string;
  tracking_number: string | null;
  grand_total: number;
  items: Array<{
    product_name: string;
    variant_info: string | null;
    unit_price: number;
    quantity: number;
    subtotal: number;
  }>;
  history: Array<{
    status: string;
    notes: string;
    created_at: string;
  }>;
}

export default function OrderTrackingModal({
  isOpen,
  onClose,
}: OrderTrackingModalProps) {
  const [orderNumber, setOrderNumber] = useState("");
  const [loading, setLoading] = useState(false);
  const [orderData, setOrderData] = useState<TrackedOrder | null>(null);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  if (!isOpen) return null;

  const handleSearch = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!orderNumber.trim()) return;

    setLoading(true);
    setErrorMsg(null);
    setOrderData(null);

    try {
      const data = await trackOrder(orderNumber.trim());
      setOrderData(data);
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : "Pesanan tidak ditemukan.";
      setErrorMsg(msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto bg-stone-950/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6">
      <div className="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-stone-200 overflow-hidden my-auto max-h-[90vh] flex flex-col">
        {/* Header */}
        <div className="p-6 border-b border-stone-200 flex items-center justify-between bg-stone-50/50">
          <div className="flex items-center gap-2">
            <PackageCheck className="w-5 h-5 text-[#782046]" />
            <h3 className="text-lg font-bold text-stone-900 font-serif">Lacak Status Pesanan</h3>
          </div>
          <button
            onClick={onClose}
            className="p-2 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-200/50 transition"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Content */}
        <div className="p-6 flex-1 overflow-y-auto space-y-5">
          <form onSubmit={handleSearch} className="space-y-3">
            <label className="block text-xs font-semibold text-stone-600">
              Masukkan Nomor Pesanan (Contoh: ORD-20261013-ABCDE)
            </label>
            <div className="flex gap-2">
              <input
                type="text"
                required
                value={orderNumber}
                onChange={(e) => setOrderNumber(e.target.value)}
                placeholder="ORD-..."
                className="flex-1 px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:ring-2 focus:ring-[#782046]/20 focus:outline-hidden"
              />
              <button
                type="submit"
                disabled={loading}
                className="px-4 py-2.5 rounded-xl bg-[#782046] hover:bg-[#621837] text-white font-semibold text-xs transition disabled:opacity-50 flex items-center gap-1.5"
              >
                {loading ? (
                  <div className="w-3.5 h-3.5 rounded-full border-2 border-white border-t-transparent animate-spin" />
                ) : (
                  <Search className="w-3.5 h-3.5" />
                )}
                <span>Lacak</span>
              </button>
            </div>
          </form>

          {errorMsg && (
            <div className="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-center gap-2">
              <AlertCircle className="w-4 h-4 shrink-0 text-rose-600" />
              <span>{errorMsg}</span>
            </div>
          )}

          {orderData && (
            <div className="space-y-4 pt-2 border-t border-stone-100">
              {/* Order Info & Status */}
              <div className="p-4 rounded-2xl bg-stone-50 border border-stone-200 space-y-2 text-xs">
                <div className="flex justify-between items-center">
                  <span className="font-bold text-stone-900">{orderData.order_number}</span>
                  <span className="px-2.5 py-0.5 rounded-full bg-rose-100 text-[#782046] font-bold uppercase text-[10px]">
                    Status: {orderData.order_status}
                  </span>
                </div>
                <div className="flex items-center gap-1.5 text-stone-500 text-[11px]">
                  <Clock className="w-3 h-3" />
                  <span>Dibuat: {orderData.created_at}</span>
                </div>
                <div className="flex items-start gap-1.5 text-stone-600 text-[11px]">
                  <MapPin className="w-3 h-3 shrink-0 mt-0.5" />
                  <span>
                    {orderData.recipient_name} - {orderData.shipping_address}, {orderData.city}
                  </span>
                </div>
                {orderData.tracking_number && (
                  <div className="flex items-center gap-1.5 text-stone-700 font-medium text-[11px] pt-1">
                    <Truck className="w-3 h-3 text-emerald-600" />
                    <span>No. Resi Pengiriman: {orderData.tracking_number}</span>
                  </div>
                )}
              </div>

              {/* Items List */}
              <div className="space-y-2">
                <span className="text-xs font-bold text-stone-800">Detail Produk:</span>
                <div className="space-y-1.5">
                  {orderData.items.map((item, i) => (
                    <div
                      key={i}
                      className="p-2.5 rounded-xl bg-white border border-stone-200 flex justify-between items-center text-xs"
                    >
                      <div>
                        <div className="font-semibold text-stone-900">{item.product_name}</div>
                        {item.variant_info && (
                          <div className="text-[11px] text-stone-500">{item.variant_info}</div>
                        )}
                      </div>
                      <div className="text-right">
                        <div className="font-bold text-stone-900">
                          {item.quantity}x Rp {item.unit_price.toLocaleString("id-ID")}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Status Timeline */}
              {orderData.history && orderData.history.length > 0 && (
                <div className="space-y-2">
                  <span className="text-xs font-bold text-stone-800">Riwayat Perjalanan:</span>
                  <div className="space-y-2 border-l-2 border-[#782046]/30 pl-3 ml-1 text-xs">
                    {orderData.history.map((hist, idx) => (
                      <div key={idx} className="relative">
                        <div className="absolute -left-[19px] top-1 w-2.5 h-2.5 rounded-full bg-[#782046]" />
                        <div className="font-bold text-stone-900 capitalize">{hist.status}</div>
                        <div className="text-stone-600 text-[11px]">{hist.notes}</div>
                        <div className="text-[10px] text-stone-400">{hist.created_at}</div>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
