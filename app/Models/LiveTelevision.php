<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveTelevision extends Model {
    use HasFactory, GlobalStatus;

    public function scopeActive() {
        return $this->where('status', 1);
    }
}
