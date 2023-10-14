<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $table = 'teachers';

    protected $guarded = false;

    public function schedules(){
        return $this->hasMany(Schedule::class,'teacher_id','id');
    }

    public function getDepartments(){
        return $this->belongsToMany(Department::class,'department_teacher','teacher_id', 'department_id');
    }
}
