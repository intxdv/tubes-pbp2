<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'avatar',
        'name',
        'phone',
        'email',
        'password',
        'role', // admin/user
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke order
    public function orders() {
        return $this->hasMany(Order::class);
    }
    // Relasi ke review
    public function reviews() {
        return $this->hasMany(Review::class);
    }
    // Relasi ke alamat (address book)
    public function addresses() {
        return $this->hasMany(\App\Models\Address::class);
    }
}
