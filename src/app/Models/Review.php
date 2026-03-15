<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'reviewer_id',
        'reviewed_id',
        'rating',
    ];
    public function reviewedUser()
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }
    public function reviewerUser()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
