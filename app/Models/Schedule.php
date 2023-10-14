<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $table = 'schedules';
    protected $guarded = false;

    public function getTeacher(){
        return $this->belongsTo(Teacher::class,'teacher_id','id');
    }

    public function getClassroom(){
        return $this->belongsTo(Classroom::class,'classroom_id','id');
    }

    public function getGroup(){
        return $this->belongsTo(Group::class,'group_id','id');
    }
}
