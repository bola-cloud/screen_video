<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tv extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'screen_id',
        'location',
        'is_active',
        'institution_id',
    ];

    // Relationships

    /**
     * Get the advertisements associated with this TV through schedules (many-to-many).
     */
    public function advertisements()
    {
        return $this->belongsToMany(Advertisement::class, 'ad_schedules')
                    ->withPivot('order', 'start_at', 'end_at')
                    ->withTimestamps();
    }

    /**
     * Get the display times for this TV.
     */
    public function displayTimes()
    {
        return $this->hasMany(TvDisplayTime::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class,'institution_id');
    }

    /**
     * Get all ad schedules for this TV.
     */
    public function schedules()
    {
        return $this->hasMany(AdSchedule::class);
    }
}
