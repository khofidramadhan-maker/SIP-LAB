<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenaltyPolicy extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'daily_fee', 'max_fee', 'active'];
}
