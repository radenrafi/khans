<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Car extends Model
{
    use HasFactory;

    public function kriterias(): BelongsToMany
    {
        return $this->belongsToMany(Kriteria::class)->withTimestamps()->withPivot('value', 'detail');
    }
}
