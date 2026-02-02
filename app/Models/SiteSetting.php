<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'instagram_url',
        'instagram_enabled',
    ];

    protected $casts = [
        'instagram_enabled' => 'boolean',
    ];
}
