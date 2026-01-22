<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * Attributes that can be mass-assigned when managing categories.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
        'parent_id',
    ];

    /**
     * Casts for keeping state in sync with the schema.
     */
    protected $casts = [
        'order' => 'integer',
        'parent_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
