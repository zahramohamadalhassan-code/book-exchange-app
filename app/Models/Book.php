<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'author', 'cover_image_url', 
        'condition', 'offer_type', 'price', 'status', 'moderation_status'
    ];

    // العلاقات
    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
}