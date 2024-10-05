<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'advertisement_id',
        'tv_id',
        'order',
        'date',
        'turns',
    ];

    // Relationships

    /**
     * Get the advertisement associated with this schedule.
     */
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Get the TV associated with this schedule.
     */
    public function tv()
    {
        return $this->belongsTo(Tv::class);
    }

    /**
     * Get the specific display times related to this schedule.
     */
    public function displayTimes()
    {
        return $this->hasMany(AdDisplayTime::class);
    }
}
