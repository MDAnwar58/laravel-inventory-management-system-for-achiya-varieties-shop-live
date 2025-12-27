<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'hero_title_part_1',
        'hero_title_part_2',
        'short_des',
        'features_title',
        'feature_title_part_2',
        'features_sub_title',
        'support_hour',
        'contact_title',
        'contact_sub_title',
    ];
}
