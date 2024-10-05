<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function tvs()
    {
        return $this->hasMany(Tv::class,'institution_id');
    }
}
