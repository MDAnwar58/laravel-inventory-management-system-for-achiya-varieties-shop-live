<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'is_auth_system',
        'low_stock_alert',
        'low_stock_alert_msg',
        'user_id',
        'domain_name',
        'domain_registration_date',
        'domain_renewal_date',
        'delete_options',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
