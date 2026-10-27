<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'lab_room_id',
        'name',
        'code',
        'condition',
        'status',
        'quantity',
        'unit',
        'notes',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function labRoom()
    {
        return $this->belongsTo(LabRoom::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_items');
    }
}
