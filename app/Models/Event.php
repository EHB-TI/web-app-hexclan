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
        'bank_account_id',
    ];

    public function members()
    {
        return $this->hasMany(EventUser::class);
    }

    // The users that belong to the event.
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->using(EventUser::class);
    }

    // The bank account that belongs to the event.
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function getMembers()
    {
        return $this->members()->pluck('user_id');
    }
}
