<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TvDisplayTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'tv_id',
        'date',
        'start_time',
        'end_time',
    ];

    // Relationships

    /**
     * Get the TV that owns this display time.
     */
    public function tv()
    {
        return $this->belongsTo(Tv::class);
    }
}
