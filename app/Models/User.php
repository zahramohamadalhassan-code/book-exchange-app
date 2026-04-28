<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id', 'university_id', 'full_name', 'email', 'password', 'phone_number', 'is_banned'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Filament: السماح فقط للمدير بالدخول للوحة الإدارة
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role && $this->role->name === 'Admin';
    }

    public function getFilamentName(): string
    {
        return $this->full_name ?? 'User';
    }

    // العلاقات
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function digitalNotes()
    {
        return $this->hasMany(DigitalNote::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // عمليات التبادل كمالك الكتاب
    public function ownedTransactions()
    {
        return $this->hasMany(Transaction::class, 'owner_id');
    }

    // عمليات التبادل كطالب للكتاب
    public function requestedTransactions()
    {
        return $this->hasMany(Transaction::class, 'requester_id');
    }

    // التقييمات التي كتبها المستخدم
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'reviewer_id');
    }

    // التقييمات التي حصل عليها المستخدم
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'reviewed_user_id');
    }

    // حساب متوسط التقييم
    public function getAverageRatingAttribute()
    {
        return $this->ratingsReceived()->avg('stars') ?? 0;
    }

    // هل المستخدم مدير؟
    public function getIsAdminAttribute(): bool
    {
        return $this->role && $this->role->name === 'Admin';
    }
}
