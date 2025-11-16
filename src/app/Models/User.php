<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Like;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    // use \Illuminate\Auth\MustVerifyEmail;

    protected $fillable = [
        'name',
        'email',
        'password',
        'post_code',
        'address',
        'build',
        'profile_image',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 商品（出品）
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // 購入履歴
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // いいね
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
