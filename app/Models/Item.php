<?php

namespace App\Models;

use App\Casts\PriceCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'price' => PriceCast::class
    ];

    // The category to which the item belongs.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * This method returns a collection of pivot model instances.
     * @return mixed
     */
    public function lines()
    {
        return $this->hasMany(ItemTransaction::class);
    }

    // The transactions that belong to the item.
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class)
            ->using(ItemTransaction::class)
            ->withPivot(['quantity', 'extended_price']);
    }
}
