<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    const NOT_VALID_ID = -1;

    protected $table = 'departments';

    protected $guarded = false;

    public function classrooms(){
        return $this->hasMany(Classroom::class,'department_id','id');
    }

    public function getFaculty(){
        return $this->belongsTo(Faculty::class,'faculty_id','id');
    }

    public function teachers(){
        return $this->belongsToMany(Teacher::class,'department_teacher','department_id', 'teacher_id');
    }
}
