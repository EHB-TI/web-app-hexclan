<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    // The event to which the category belongs.
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // The items that belong to the category
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
