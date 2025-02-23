<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['full_name', 'phone', 'password', 'store_id', 'is_admin'];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    }
}
