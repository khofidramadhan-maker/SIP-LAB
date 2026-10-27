<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDocument extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'document_type', 'file_name', 'file_path'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
