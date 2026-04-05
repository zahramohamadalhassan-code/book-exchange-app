<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'book_id', 'requester_id', 'owner_id', 'meeting_date', 'meeting_time', 'meeting_location', 'status'
    ];

    public function book() { return $this->belongsTo(Book::class); }
    public function requester() { return $this->belongsTo(User::class, 'requester_id'); }
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function rating() { return $this->hasOne(Rating::class); }
}