<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * In-memory cache for the current request cycle to avoid duplicate DB queries.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $runtimeCache = null;

    /**
     * Retrieve all settings as key-value map, cached for 1 hour.
     */
    public static function allSettingsMap(): array
    {
        if (static::$runtimeCache !== null) {
            return static::$runtimeCache;
        }

        try {
            static::$runtimeCache = \Illuminate\Support\Facades\Cache::remember('store_settings_map', 3600, function () {
                return static::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            static::$runtimeCache = [];
        }

        return static::$runtimeCache ?? [];
    }

    public static function get(string $key, $default = null)
    {
        $settings = static::allSettingsMap();

        if (array_key_exists($key, $settings) && $settings[$key] !== null && $settings[$key] !== '') {
            return $settings[$key];
        }

        $configKeyMap = [
            'store_name' => 'store.name',
            'store_tagline' => 'store.tagline',
            'store_phone' => 'store.phone',
            'store_whatsapp' => 'store.whatsapp',
            'store_email' => 'store.email',
            'store_address' => 'store.address',
            'store_instagram' => 'store.instagram',
            'working_hours' => 'store.working_hours',
            'payment_expiry_minutes' => 'store.payment_expiry_minutes',
        ];

        if (isset($configKeyMap[$key])) {
            return config($configKeyMap[$key], $default);
        }

        return $default;
    }

    public static function whatsappUrl(?string $message = null): string
    {
        $wa = static::get('store_whatsapp', config('store.whatsapp', '628135487734'));
        $waClean = preg_replace('/[^0-9]/', '', (string) $wa);
        if (str_starts_with($waClean, '0')) {
            $waClean = '62' . substr($waClean, 1);
        }

        $url = 'https://wa.me/' . $waClean;
        if (!empty($message)) {
            $url .= '?text=' . urlencode($message);
        }
        return $url;
    }

    public static function set(string $key, $value, ?string $description = null): self
    {
        $result = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );

        // Invalidate both runtime and persistent cache
        static::$runtimeCache = null;
        try {
            \Illuminate\Support\Facades\Cache::forget('store_settings_map');
        } catch (\Throwable $e) {
            // Ignore cache store issues
        }

        return $result;
    }
}
