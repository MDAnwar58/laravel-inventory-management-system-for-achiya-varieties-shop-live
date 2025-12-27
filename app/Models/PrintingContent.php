<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintingContent extends Model
{
    protected $fillable = [
        'phone_number',
        'phone_number2',
        'location',
        'short_desc',
    ];
}
