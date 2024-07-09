<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Guideline
 *
 * @property string $description
 * @property float $score
 * @property Team $team
 * @property Collection<GuidelineBullet> $bullets
 * @property Collection<GuidelineTicket> $tickets
 */
class Guideline extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function team(): BelongsTo
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
