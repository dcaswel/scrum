<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class GuidelineBullet
 *
 * @property string $body
 *
 * @property Guideline $guideline
 */
class GuidelineBullet extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function guideline(): BelongsTo
    {
        return $this->belongsTo(Guideline::class);
    }
}
