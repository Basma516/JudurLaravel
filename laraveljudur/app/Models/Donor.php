<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'donor_id_number',
    ];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

