<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guideline extends Model
{
    use HasFactory;

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function bullets(): HasMany
    {
        return $this->hasMany(GuidelineBullet::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(GuidelineTicket::class);
    }
}
