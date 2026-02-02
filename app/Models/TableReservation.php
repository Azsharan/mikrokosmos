<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TableReservation extends Model
{
    use HasFactory;

    public const TOTAL_TABLES = 4;
    public const MAX_PARTY_SIZE = 6;
    public const SESSION_DURATION_MINUTES = 120;

    public const STATUS_PENDING = 0;
    public const STATUS_CONFIRMED = 1;
    public const STATUS_CANCELLED = 2;

    protected $fillable = [
        'shop_user_id',
        'table_number',
        'party_size',
        'reserved_for',
        'reserved_until',
        'status',
        'notes',
        'code',
    ];

    protected $casts = [
        'reserved_for' => 'datetime',
        'reserved_until' => 'datetime',
        'party_size' => 'integer',
        'table_number' => 'integer',
        'status' => 'integer',
    ];

    public function shopUser(): BelongsTo
    {
        return $this->belongsTo(ShopUser::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return static::statusLabels()[$this->status] ?? __('Desconocido');
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => __('Pendiente'),
            self::STATUS_CONFIRMED => __('Confirmada'),
            self::STATUS_CANCELLED => __('Cancelada'),
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TableReservation $reservation) {
            if (! $reservation->code) {
                $reservation->code = static::generateUniqueCode();
            }
        });
    }

    protected static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }
}
