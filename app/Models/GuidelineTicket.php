<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuidelineTicket extends Model
{
    use HasFactory;

    public function guideline(): BelongsTo
    {
        return $this->belongsTo(Guideline::class);
    }
}
