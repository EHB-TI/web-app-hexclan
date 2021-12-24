<?php

namespace App\Models;

use App\Traits\Accountable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, Accountable;

    // Required because primary key is uuid.
    //public $incrementing = false;

    protected $guarded = [];

    // The bank account that belongs to the event.
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    // The categories that belong to the event.
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function items()
    {
        return $this->hasManyThrough(Item::class, Category::class);
    }

    /**
     * This method returns a collection of pivot model instances.
     * @return mixed
     */
    public function roles()
    {
        return $this->hasMany(EventUser::class);
    }

    // The transactions that belong to the event.
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // The users that belong to the event.
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->using(EventUser::class)
            ->withPivot('ability');
    }

    // Returns user role on a specific event.
    public function getManager()
    {
        return $this->roles->firstWhere('ability', '=', 'manager');
    }
}
