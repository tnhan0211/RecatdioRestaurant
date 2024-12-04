<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'booking_date', 
        'number_of_people', 'status', 'special_request'
    ];

    protected $casts = [
        'booking_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookingMenus()
    {
        return $this->hasMany(BookingMenu::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->bookingMenus->sum('subtotal');
    }
} 