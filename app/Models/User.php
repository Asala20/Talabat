<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['full_name', 'phone', 'password', 'store_id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
