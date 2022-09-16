<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class GuidelineTicket
 *
 * @property string $ticket_number
 * @property string $url
 *
 * @property Guideline $guideline
 */
class GuidelineTicket extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function guideline(): BelongsTo
    {
        return $this->belongsTo(Guideline::class);
    }
}
