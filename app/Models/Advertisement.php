<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'brand',
        'video_link',
        'video_duration',
        'client_id',
    ];

    // Relationships

    /**
     * Get the TVs associated with this advertisement through schedules (many-to-many).
     */
    public function tvs()
    {
        return $this->belongsToMany(Tv::class, 'ad_schedules')
                    ->withPivot('order', 'start_at', 'end_at')
                    ->withTimestamps();
    }

    /**
     * Get the client who owns the advertisement.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get all ad schedules for this advertisement.
     */
    public function schedules()
    {
        return $this->hasMany(AdSchedule::class);
    }
}
