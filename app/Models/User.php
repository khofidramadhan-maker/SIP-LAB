<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'student_number',
        'program_study',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}

class StudentProfile extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'nim',
        'faculty',
        'study_program',
        'advisor_name',
        'advisor_nip',
        'academic_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
