"use client";

import React, { useState } from "react";
import Image from "next/image";
import confetti from "canvas-confetti";
import {
  CartItem,
  OrderSuccessData,
  PaymentMethod,
  ShippingMethod,
} from "@/types/ecommerce";
import { submitCheckout } from "@/services/api";
import { STORE_CONFIG } from "@/config/store";
import {
  X,
  CreditCard,
  Truck,
  CheckCircle2,
  Copy,
  Check,
  AlertCircle,
  QrCode,
  Heart,
} from "lucide-react";

interface CheckoutModalProps {
  isOpen: boolean;
  onClose: () => void;
  cartItems: CartItem[];
  shippingMethods: ShippingMethod[];
  paymentMethods: PaymentMethod[];
  onOrderSuccess: (order: OrderSuccessData) => void;
}

export default function CheckoutModal({
  isOpen,
  onClose,
  cartItems,
  shippingMethods,
  paymentMethods,
  onOrderSuccess,
}: CheckoutModalProps) {
  const [formData, setFormData] = useState({
    recipient_name: "Siti Rahmawati",
    recipient_phone: "085711223344",
    full_address: "Jl. Mawar Melati No. 45, RT 03 / RW 07, Kebayoran Baru",
    city: "Jakarta Selatan",
    postal_code: "12150",
    customer_email: "siti.rahma@example.com",
    greeting_card_note: "Selamat atas kelulusan wisuda, semoga sukses selalu menyertai langkahmu!",
    customer_note: "Tolong kirimkan sebelum jam 14.00 WIB ya florist.",
  });

  const [selectedShippingId, setSelectedShippingId] = useState<number>(
    shippingMethods[0]?.id || 1
  );
  const [selectedPaymentId, setSelectedPaymentId] = useState<number>(
    paymentMethods[0]?.id || 1
  );

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [completedOrder, setCompletedOrder] = useState<OrderSuccessData | null>(null);
  const [copiedAccount, setCopiedAccount] = useState(false);

  if (!isOpen) return null;

  const selectedShipping =
    shippingMethods.find((s) => s.id === selectedShippingId) || shippingMethods[0];
  const selectedPayment =
    paymentMethods.find((p) => p.id === selectedPaymentId) || paymentMethods[0];

  const subtotal = cartItems.reduce((acc, item) => {
    const unitPrice = item.variant
      ? item.product.effective_price + item.variant.additional_price
      : item.product.effective_price;
    return acc + unitPrice * item.quantity;
  }, 0);

  const shippingCost = selectedShipping?.cost || 0;
  const grandTotal = subtotal + shippingCost;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMessage(null);

    try {
      const payload = {
        recipient_name: formData.recipient_name,
        recipient_phone: formData.recipient_phone,
        full_address: formData.full_address,
        city: formData.city,
        postal_code: formData.postal_code,
        customer_email: formData.customer_email,
        shipping_method_id: selectedShippingId,
        payment_method_id: selectedPaymentId,
        customer_note: formData.customer_note,
        greeting_card_note: formData.greeting_card_note,
        items: cartItems.map((item) => ({
          product_id: item.product.id,
          variant_id: item.variant?.id || null,
          quantity: item.quantity,
        })),
      };

      const result = await submitCheckout(payload);
      setCompletedOrder(result);
      onOrderSuccess(result);

      // Trigger Confetti
      try {
        confetti({
          particleCount: 80,
          spread: 70,
          origin: { y: 0.6 },
        });
      } catch {
        // Confetti fallback
      }
    } catch (err: unknown) {
      const msg = err instanceof Error ? err.message : "Gagal memproses pesanan. Silakan coba lagi.";
      setErrorMessage(msg);
    } finally {
      setIsSubmitting(false);
    }
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    setCopiedAccount(true);
    setTimeout(() => setCopiedAccount(false), 2000);
  };

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto bg-stone-950/60 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6">
      <div className="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-stone-200 overflow-hidden my-auto max-h-[92vh] flex flex-col">
        {/* Header */}
        <div className="p-6 border-b border-stone-200 flex items-center justify-between bg-stone-50/50">
          <div>
            <h3 className="text-xl font-bold text-stone-900 font-serif">
              {completedOrder ? "Pesanan Berhasil Dibuat!" : "Checkout & Pembayaran"}
            </h3>
            <p className="text-xs text-stone-500 mt-0.5">
              {completedOrder
                ? `Nomor Faktur: ${completedOrder.order_number}`
                : "Lengkapi data pengiriman dan pilih metode pembayaran"}
            </p>
          </div>
          <button
            onClick={onClose}
            className="p-2 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-200/50 transition"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Content Area */}
        <div className="flex-1 overflow-y-auto p-6">
          {completedOrder ? (
            /* Order Success View */
            <div className="space-y-6 text-center">
              <div className="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner">
                <CheckCircle2 className="w-10 h-10" />
              </div>

              <div>
                <h4 className="text-2xl font-bold text-stone-900">
                  Terima Kasih, {completedOrder.recipient_name}!
                </h4>
                <p className="text-sm text-stone-600 mt-1">
                  Pesanan bucket bunga Anda telah tersimpan dan siap diproses oleh tim florist kami.
                </p>
              </div>

              {/* Payment Details Box */}
              <div className="p-5 rounded-2xl bg-rose-50/60 border border-rose-200/80 text-left space-y-4">
                <div className="flex items-center justify-between pb-3 border-b border-rose-200/60">
                  <div>
                    <span className="text-xs text-stone-500">Total Tagihan:</span>
                    <div className="text-2xl font-extrabold text-[#782046]">
                      Rp {completedOrder.grand_total.toLocaleString("id-ID")}
                    </div>
                  </div>
                  <span className="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase">
                    Menunggu Pembayaran
                  </span>
                </div>

                {/* QRIS / Account Information */}
                <div>
                  <div className="text-xs font-bold text-stone-800 uppercase tracking-wide flex items-center gap-1.5 mb-2">
                    <QrCode className="w-4 h-4 text-[#782046]" />
                    <span>Metode: {completedOrder.payment_method.name}</span>
                  </div>

                  {completedOrder.payment_method.qr_image_url && (
                    <div className="flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-stone-200 my-3 shadow-xs">
                      <div className="relative w-72 h-[380px] max-w-full rounded-xl overflow-hidden bg-white">
                        <Image
                          src={completedOrder.payment_method.qr_image_url || "/images/qris-mocyan.jpg"}
                          alt="QRIS MocYy, Kerajinan"
                          fill
                          className="object-contain"
                          priority
                        />
                      </div>
                      <div className="mt-3 text-center">
                        <div className="text-xs font-bold text-stone-900">MocYy, Kerajinan</div>
                        <div className="text-[11px] text-stone-500 font-mono">NMID: ID1026613806474 (A01)</div>
                        <p className="text-[11px] text-stone-500 mt-1 max-w-xs">
                          Scan QRIS resmi di atas menggunakan m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet (GoPay, OVO, ShopeePay, DANA)
                        </p>
                      </div>
                    </div>
                  )}

                  {completedOrder.payment_method.account_number && (
                    <div className="p-3 bg-white rounded-xl border border-stone-200 flex items-center justify-between">
                      <div>
                        <div className="text-[11px] text-stone-400">Nomor Rekening / Akun:</div>
                        <div className="text-sm font-bold text-stone-900">
                          {completedOrder.payment_method.account_number}
                        </div>
                        <div className="text-[11px] text-stone-500">
                          a.n. {completedOrder.payment_method.account_name}
                        </div>
                      </div>
                      <button
                        onClick={() =>
                          copyToClipboard(completedOrder.payment_method.account_number || "")
                        }
                        className="px-3 py-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold flex items-center gap-1 transition"
                      >
                        {copiedAccount ? <Check className="w-3.5 h-3.5 text-emerald-600" /> : <Copy className="w-3.5 h-3.5" />}
                        <span>{copiedAccount ? "Tersalin" : "Salin"}</span>
                      </button>
                    </div>
                  )}

                  <div className="mt-3 p-3 bg-stone-100 rounded-xl text-xs text-stone-600 whitespace-pre-line leading-relaxed">
                    {completedOrder.payment_method.instructions}
                  </div>
                </div>
              </div>

              <div className="flex flex-col sm:flex-row gap-3 pt-2">
                <a
                  href={STORE_CONFIG.getWhatsAppLink(`Halo MocYy, saya sudah membuat pesanan dengan nomor ${completedOrder.order_number}. Berikut bukti transfer pembayarannya.`)}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition flex items-center justify-center gap-2 shadow-sm"
                >
                  <span>Konfirmasi Pembayaran via WhatsApp</span>
                </a>
                <button
                  onClick={onClose}
                  className="py-3 px-6 rounded-xl bg-stone-200 hover:bg-stone-300 text-stone-800 font-semibold text-xs transition"
                >
                  Tutup
                </button>
              </div>
            </div>
          ) : (
            /* Checkout Form View */
            <form onSubmit={handleSubmit} className="space-y-6">
              {errorMessage && (
                <div className="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-center gap-2">
                  <AlertCircle className="w-4 h-4 shrink-0 text-rose-600" />
                  <span>{errorMessage}</span>
                </div>
              )}

              {/* Recipient Details */}
              <div className="space-y-3">
                <h4 className="text-xs font-bold text-stone-900 uppercase tracking-wider">
                  1. Informasi Penerima & Pengiriman
                </h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-semibold text-stone-600 mb-1">
                      Nama Lengkap Penerima
                    </label>
                    <input
                      type="text"
                      required
                      value={formData.recipient_name}
                      onChange={(e) => setFormData({ ...formData, recipient_name: e.target.value })}
                      className="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:ring-2 focus:ring-[#782046]/20 focus:outline-hidden"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-semibold text-stone-600 mb-1">
                      Nomor Telepon / WhatsApp
                    </label>
                    <input
                      type="tel"
                      required
                      value={formData.recipient_phone}
                      onChange={(e) => setFormData({ ...formData, recipient_phone: e.target.value })}
                      className="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:ring-2 focus:ring-[#782046]/20 focus:outline-hidden"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-[11px] font-semibold text-stone-600 mb-1">
                    Email Customer (Untuk Akun & Bukti Pembayaran) *
                  </label>
                  <input
                    type="email"
                    required
                    value={formData.customer_email}
                    onChange={(e) => setFormData({ ...formData, customer_email: e.target.value })}
                    placeholder="nama@email.com"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:ring-2 focus:ring-[#782046]/20 focus:outline-hidden"
                  />
                  <p className="text-[10px] text-stone-400 mt-0.5">
                    Akun dan konfirmasi faktur pesanan akan dikaitkan ke email ini.
                  </p>
                </div>

                <div>
                  <label className="block text-[11px] font-semibold text-stone-600 mb-1">
                    Alamat Lengkap Pengiriman (Jalan, Nomor, Patokan)
                  </label>
                  <textarea
                    rows={2}
                    required
                    value={formData.full_address}
                    onChange={(e) => setFormData({ ...formData, full_address: e.target.value })}
                    className="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:ring-2 focus:ring-[#782046]/20 focus:outline-hidden"
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-semibold text-stone-600 mb-1">
                      Kota / Wilayah
                    </label>
                    <input
                      type="text"
                      required
                      value={formData.city}
                      onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                      className="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:ring-2 focus:ring-[#782046]/20 focus:outline-hidden"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-semibold text-stone-600 mb-1">
                      Kode Pos
                    </label>
                    <input
                      type="text"
                      required
                      value={formData.postal_code}
                      onChange={(e) => setFormData({ ...formData, postal_code: e.target.value })}
                      className="w-full px-3.5 py-2.5 rounded-xl border border-stone-200 text-xs focus:ring-2 focus:ring-[#782046]/20 focus:outline-hidden"
                    />
                  </div>
                </div>
              </div>

              {/* Greeting Card Note */}
              <div className="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/60 space-y-2">
                <div className="flex items-center gap-1.5 text-xs font-bold text-amber-900">
                  <Heart className="w-3.5 h-3.5 text-rose-500 fill-rose-500" />
                  <span>Pesan Khusus di Kartu Ucapan (Gratis)</span>
                </div>
                <textarea
                  rows={2}
                  value={formData.greeting_card_note}
                  onChange={(e) => setFormData({ ...formData, greeting_card_note: e.target.value })}
                  placeholder="Contoh: Selamat ulang tahun ke-25 untuk sahabat terbaikku..."
                  className="w-full px-3 py-2 rounded-xl border border-amber-200/80 bg-white text-xs text-stone-800 focus:outline-hidden"
                />
              </div>

              {/* Shipping Method */}
              <div className="space-y-2">
                <h4 className="text-xs font-bold text-stone-900 uppercase tracking-wider flex items-center gap-1.5">
                  <Truck className="w-3.5 h-3.5 text-stone-600" />
                  <span>2. Opsi Pengiriman</span>
                </h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {shippingMethods.map((sm) => {
                    const isSelected = selectedShippingId === sm.id;
                    return (
                      <button
                        type="button"
                        key={sm.id}
                        onClick={() => setSelectedShippingId(sm.id)}
                        className={`p-3 rounded-xl border text-left text-xs transition ${
                          isSelected
                            ? "border-[#782046] bg-rose-50/50 ring-2 ring-[#782046]/20"
                            : "border-stone-200 hover:border-stone-300"
                        }`}
                      >
                        <div className="font-bold text-stone-900">{sm.name}</div>
                        <div className="text-[11px] text-stone-500">{sm.estimated_time}</div>
                        <div className="font-extrabold text-[#782046] mt-1">
                          Rp {sm.cost.toLocaleString("id-ID")}
                        </div>
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Payment Method - Exclusive QRIS */}
              <div className="space-y-2">
                <h4 className="text-xs font-bold text-stone-900 uppercase tracking-wider flex items-center gap-1.5">
                  <CreditCard className="w-3.5 h-3.5 text-stone-600" />
                  <span>3. Metode Pembayaran Resmi (Eksklusif QRIS)</span>
                </h4>
                <div className="grid grid-cols-1 gap-2">
                  {paymentMethods.map((pm) => {
                    const isSelected = selectedPaymentId === pm.id;
                    return (
                      <button
                        type="button"
                        key={pm.id}
                        onClick={() => setSelectedPaymentId(pm.id)}
                        className={`p-3.5 rounded-2xl border text-left text-xs transition flex items-center justify-between ${
                          isSelected
                            ? "border-[#782046] bg-rose-50/60 ring-2 ring-[#782046]/20"
                            : "border-stone-200 hover:border-stone-300"
                        }`}
                      >
                        <div>
                          <div className="font-bold text-stone-900 flex items-center gap-2">
                            <span>{pm.name}</span>
                            <span className="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                              Instan & Aman
                            </span>
                          </div>
                          <div className="text-[11px] text-stone-500 mt-0.5 font-mono">
                            {pm.account_number || "NMID: ID1026613806474 (A01)"} &bull; a.n. {pm.account_name || "MocYy, Kerajinan"}
                          </div>
                        </div>
                        <QrCode className="w-6 h-6 text-[#782046] shrink-0" />
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Order Summary Breakdown */}
              <div className="p-4 rounded-2xl bg-stone-50 border border-stone-200/80 space-y-2 text-xs">
                <div className="flex justify-between text-stone-600">
                  <span>Subtotal Produk ({cartItems.length} item)</span>
                  <span className="font-medium text-stone-900">
                    Rp {subtotal.toLocaleString("id-ID")}
                  </span>
                </div>
                <div className="flex justify-between text-stone-600">
                  <span>Ongkos Kirim ({selectedShipping?.name})</span>
                  <span className="font-medium text-stone-900">
                    Rp {shippingCost.toLocaleString("id-ID")}
                  </span>
                </div>
                <div className="flex justify-between text-sm font-bold text-stone-900 pt-2 border-t border-stone-200">
                  <span>Total Pembayaran</span>
                  <span className="text-base text-[#782046]">
                    Rp {grandTotal.toLocaleString("id-ID")}
                  </span>
                </div>
              </div>

              {/* Submit CTA */}
              <button
                type="submit"
                disabled={isSubmitting}
                className="w-full py-4 rounded-2xl bg-[#782046] hover:bg-[#621837] text-white font-semibold text-sm shadow-xl shadow-rose-900/20 active:scale-[0.98] transition disabled:opacity-50 flex items-center justify-center gap-2"
              >
                {isSubmitting ? (
                  <>
                    <div className="w-4 h-4 rounded-full border-2 border-white border-t-transparent animate-spin" />
                    <span>Memproses Pesanan Aman...</span>
                  </>
                ) : (
                  <span>Konfirmasi & Dapatkan Kode Pembayaran</span>
                )}
              </button>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}
