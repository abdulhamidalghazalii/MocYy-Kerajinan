<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'shipping_method_id',
        'payment_method_id',
        'recipient_name',
        'recipient_phone',
        'shipping_address',
        'city',
        'postal_code',
        'customer_note',
        'greeting_card_note',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'grand_total',
        'payment_status',
        'order_status',
        'tracking_number',
        'cancellation_reason',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('created_at');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPendingReview(): bool
    {
        return $this->payment_status === 'pending_review';
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    public function isRejected(): bool
    {
        return $this->payment_status === 'rejected';
    }

    public function canUploadPaymentProof(): bool
    {
        return in_array($this->payment_status, ['unpaid', 'rejected']) && $this->order_status !== 'cancelled';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'Menunggu Pembayaran',
            'pending_review' => 'Menunggu Verifikasi Admin',
            'paid' => 'Pembayaran Terverifikasi',
            'rejected' => 'Pembayaran Ditolak',
            'expired' => 'Kadaluarsa',
            'refunded' => 'Dikembalikan',
            default => ucfirst($this->payment_status),
        };
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'bg-amber-100 text-amber-800 border-amber-200',
            'pending_review' => 'bg-blue-100 text-blue-800 border-blue-200',
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
            'expired' => 'bg-gray-100 text-gray-800 border-gray-200',
            'refunded' => 'bg-purple-100 text-purple-800 border-purple-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getOrderStatusLabelAttribute(): string
    {
        return match ($this->order_status) {
            'pending_payment' => 'Menunggu Pembayaran',
            'processing' => 'Pesanan Dikonfirmasi',
            'in_production' => 'Sedang Dirangkai (Produksi)',
            'ready_to_ship' => 'Siap Dikirim / Diambil',
            'shipped' => 'Dalam Pengiriman',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->order_status)),
        };
    }

    public function getOrderStatusBadgeClassAttribute(): string
    {
        return match ($this->order_status) {
            'pending_payment' => 'bg-amber-50 text-amber-700 border-amber-300',
            'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-300',
            'in_production' => 'bg-purple-50 text-purple-700 border-purple-300',
            'ready_to_ship' => 'bg-teal-50 text-teal-700 border-teal-300',
            'shipped' => 'bg-cyan-50 text-cyan-700 border-cyan-300',
            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-300',
            default => 'bg-gray-50 text-gray-700 border-gray-300',
        };
    }
}
