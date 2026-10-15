<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'account_name',
        'account_number',
        'qr_image_path',
        'instructions',
        'expiry_minutes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'expiry_minutes' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getQrImageUrlAttribute(): ?string
    {
        if ($this->qr_image_path) {
            if (str_starts_with($this->qr_image_path, 'http')) {
                return $this->qr_image_path;
            }
            if (file_exists(public_path('images/' . basename($this->qr_image_path)))) {
                return asset('images/' . basename($this->qr_image_path));
            }
            if (file_exists(public_path('storage/' . $this->qr_image_path))) {
                return asset('storage/' . $this->qr_image_path);
            }
            return asset('storage/' . $this->qr_image_path);
        }

        if (strtolower($this->code ?? '') === 'qris') {
            return asset('images/qris-mocyan.jpg');
        }

        return null;
    }
}
