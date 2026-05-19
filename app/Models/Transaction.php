<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id', 'offered_book_id', 'requester_id', 'owner_id',
        'meeting_date', 'meeting_time', 'meeting_location', 'status'
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'meeting_time' => 'datetime:H:i',
        ];
    }

    // العلاقات
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function offeredBook()
    {
        return $this->belongsTo(Book::class, 'offered_book_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
