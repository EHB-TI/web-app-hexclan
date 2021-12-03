<?php

namespace App\Models;

use App\Casts\PriceCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total' => PriceCast::class
    ];

    // The items that belong to the transaction.
    public function items()
    {
        return $this->belongsToMany(Item::class)
            ->using(ItemTransaction::class)
            ->withPivot(['quantity', 'extended_price']);
    }

    /**
     * This method returns a collection of pivot model instances.
     * @return mixed
     */
    public function lines()
    {
        return $this->hasMany(ItemTransaction::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
