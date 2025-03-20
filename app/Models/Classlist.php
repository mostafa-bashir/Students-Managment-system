<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassList extends Model
{
    use HasFactory;

    protected $table = 'classlists'; // Explicitly define the table name
    protected $fillable = ['className']; // Add other fillable fields if needed

    // Define relationships
    public function students()
    {
        return $this->hasMany(Student::class, 'Class', 'className');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
}
