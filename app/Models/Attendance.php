<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'session_id',
        'attendance',
        'date'
    ];

    // Define relationships
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // public function class()
    // {
    //     return $this->belongsTo(ClassList::class, 'class_id');
    // }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
