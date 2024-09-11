<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdDisplayTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_schedule_id',
        'display_date',
        'display_time',
    ];

    // Relationships

    /**
     * Get the ad schedule that owns this display time.
     */
    public function adSchedule()
    {
        return $this->belongsTo(AdSchedule::class);
    }
}
