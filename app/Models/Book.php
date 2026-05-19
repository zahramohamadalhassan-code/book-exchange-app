<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'author', 'cover_image_url',
        'pages_count', 'condition', 'offer_type', 'exchange_for', 'price', 'status', 'moderation_status'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'pages_count' => 'integer',
        ];
    }

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // علاقة المفضلة (Polymorphic)
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    // Scopes مفيدة
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('moderation_status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('moderation_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('moderation_status', 'approved');
    }
}
