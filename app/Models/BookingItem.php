<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'asset_id', 'quantity', 'notes'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
