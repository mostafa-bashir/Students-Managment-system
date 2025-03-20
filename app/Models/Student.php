<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name',
        'Phone',
        'Birth',
        'Gender',
        'Class',
        'Address',
        'fatherName',
        'fatherPhone',
        'Status'
    ];

    // Define relationships
    public function class()
    {
        return $this->belongsTo(ClassList::class, 'Class', 'className');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
}
