<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model {
    protected $casts = [
        'thumbnail' => 'object',
    ];

    public function video() {
        return $this->hasOne(Video::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function videoReport() {
        return $this->hasMany(VideoReport::class);
    }

    public function scopeHasVideo() {
        return $this->where('status', 1)->whereHas('video');
    }
}
