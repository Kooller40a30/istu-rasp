<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classrooms';
    protected $guarded = false;

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }

    public function getFaculty(){
        return $this->belongsTo(Faculty::class,'faculty_id','id');
    }

    public function getDepartment(){
        return $this->belongsTo(Department::class,'department_id','id');
    }
}
