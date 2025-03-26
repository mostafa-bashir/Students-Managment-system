<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = ['class_id', 'title', 'date'];

    protected $dates = ['date'];
    
    public function class()
    {
        return $this->belongsTo(ClassList::class, 'class_id');
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}