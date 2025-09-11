<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    public function photos()
    {
        return $this->hasMany(\App\Models\Photo::class);
    }

    public function rateCalendars()
    {
        return $this->hasMany(\App\Models\RateCalendar::class);
    }
    
    public function reservations()
    {
        return $this->hasMany(\App\Models\Reservation::class);
    }
}
