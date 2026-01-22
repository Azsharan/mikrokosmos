<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'event_type_id',
        'category_id',
        'slug',
        'description',
        'start_at',
        'end_at',
        'location',
        'is_online',
        'capacity',
        'is_published',
        'cover_image',
        'metadata',
    ];

    protected $casts = [
        'event_type_id' => 'integer',
        'category_id' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_online' => 'boolean',
        'is_published' => 'boolean',
        'capacity' => 'integer',
        'metadata' => 'array',
    ];

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function remainingCapacity(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        $registered = property_exists($this, 'registrations_count')
            ? $this->registrations_count
            : $this->registrations()->count();

        return max($this->capacity - $registered, 0);
    }

    public function isFull(): bool
    {
        $remaining = $this->remainingCapacity();

        return $remaining !== null && $remaining <= 0;
    }

    public function hasRegistrationForUser(?ShopUser $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->relationLoaded('registrations')) {
            return $this->registrations->contains('shop_user_id', $user->getKey());
        }

        return $this->registrations()
            ->where('shop_user_id', $user->getKey())
            ->exists();
    }
}
