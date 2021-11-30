<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    // The bank account that belongs to the event.
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
