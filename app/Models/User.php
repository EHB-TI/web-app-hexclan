<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Required because primary key is uuid.
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'is_active',
        'is_admin',
        'pin_code',
        'pin_code_timestamp'
        //'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        //'email_verified_at' => 'datetime',
        'id' => 'string',
        'is_active' => 'boolean',
        'is_admin' => 'boolean',
        'pin_code_timestamp' => 'datetime'
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('role');
    }
}
