<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        int $adminId,
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $details = null
    ): self {
        return static::create([
            'user_id' => $adminId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ]);
    }

    public static function log(
        int $adminId,
        string $action,
        string|array|null $details = null,
        mixed $model = null
    ): self {
        $modelType = is_object($model) ? get_class($model) : null;
        $modelId = is_object($model) && isset($model->id) ? $model->id : (is_numeric($model) ? (int) $model : null);
        $detailsArr = is_array($details) ? $details : ($details ? ['message' => $details] : null);

        return static::record($adminId, $action, $modelType, $modelId, $detailsArr);
    }
}
