<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'city',
        'zip_code',
        'present_address',
        'address',
        'card_front_side',
        'card_back_side',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
