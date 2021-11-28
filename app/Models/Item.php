<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // The items sold at the event belong to one event only.
    public function events()
    {
        return $this->belongsTo(Event::class);
    }
}
