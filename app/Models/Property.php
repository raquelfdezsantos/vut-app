<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    public function photos()
    {
        return $this->hasMany(\App\Models\Photo::class);
    }

    public function rateCalendar()
    {
        return $this->hasMany(RateCalendar::class, 'property_id');
    }
    
    public function reservations()
    {
        return $this->hasMany(\App\Models\Reservation::class);
    }
}
