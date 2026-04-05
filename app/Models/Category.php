<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['university_name', 'faculty_name', 'department_name', 'study_year'];

    // العلاقات
    public function books() { return $this->hasMany(Book::class); }
    public function digitalNotes() { return $this->hasMany(DigitalNote::class); }
}