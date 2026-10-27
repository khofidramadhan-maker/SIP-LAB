<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lab_room_id',
        'title',
        'purpose',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'request_type',
        'document_recommendation',
        'supervisor_name',
        'supervisor_nip',
        'status',
        'notes',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function labRoom()
    {
        return $this->belongsTo(LabRoom::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function documents()
    {
        return $this->hasMany(BookingDocument::class);
    }

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

    public function approval()
    {
        return $this->hasOne(Approval::class);
    }

    public function handover()
    {
        return $this->hasOne(Handover::class);
    }

    public function returnRecord()
    {
        return $this->hasOne(ReturnRecord::class);
    }
}
