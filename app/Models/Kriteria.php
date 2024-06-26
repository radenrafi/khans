<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use HasFactory;

    public function values(): HasMany
    {
        return $this->hasMany(ValueKriteria::class);
    }

    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class)->withTimestamps();
    }
}
