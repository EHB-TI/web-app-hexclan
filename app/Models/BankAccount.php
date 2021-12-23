<?php

namespace App\Models;

use App\Traits\Accountable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory, Accountable;

    // Required because primary key is uuid.
    //public $incrementing = false;

    protected $guarded = [];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
