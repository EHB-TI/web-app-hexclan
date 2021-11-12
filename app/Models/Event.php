<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Required because primary key is uuid.
    //public $incrementing = false;

    protected $guarded = [];

    protected $hidden = [
        'bank_account_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class); //define pivot here too?
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
