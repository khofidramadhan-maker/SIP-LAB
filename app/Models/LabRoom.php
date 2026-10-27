<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'location',
        'capacity',
        'description',
        'status',
        'open_time',
        'close_time',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
