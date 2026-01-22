<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'name',
        'email',
        'phone',
        'quantity',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
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
