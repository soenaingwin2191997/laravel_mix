<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use GlobalStatus;

    public function scopeActive() {
        return $this->where('status', 1);
    }

    public function subcategories() {
        return $this->hasMany(SubCategory::class);
    }
}
