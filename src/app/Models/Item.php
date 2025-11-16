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
}
