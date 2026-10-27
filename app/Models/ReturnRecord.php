<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRecord extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'checked_by', 'returned_at', 'condition_status', 'late_hours', 'notes'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
