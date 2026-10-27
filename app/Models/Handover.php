<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Handover extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'handover_by', 'received_by', 'handover_at', 'notes'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
