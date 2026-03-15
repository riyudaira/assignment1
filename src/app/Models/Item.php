<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'buyer_id',
        'status',
        'name',
        'price',
        'brand',
        'description',
        'image_path',
        'condition',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isPurchased(): bool
    {
        return $this->purchases()->exists();
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function isSold()
    {
        return $this->purchase()->exists();
    }
    public function getImageUrlAttribute()
    {
        return Str::startsWith($this->image_path, 'http')
            ? $this->image_path
            : asset('storage/' . $this->image_path);
    }
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
