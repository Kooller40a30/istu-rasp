<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $table = 'schedules';
    protected $guarded = false;

    public function getTeachers()
    {
        return $this->hasManyThrough(Teacher::class, TeacherSchedule::class, 'schedule_id', 'id', 'id', 'teacher_id');
    }

    public function getClassroom()
    {
        return $this->belongsTo(Classroom::class,'classroom_id','id');
    }

    public function getGroups()
    {
        return $this->through('groupSchedule')->has('groups');
    }

    public function getClass()
    {
        return $this->belongsTo(ClassModel::class, 'class', 'id');
    }

    public function groupSchedule()
    {
        return $this->hasMany(GroupSchedule::class, 'schedule_id', 'id');
    }

    public function getDiscipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id', 'id');
    }

    public function getTypeDiscipline()
    {
        return $this->belongsTo(TypeDiscipline::class, 'type_discipline_id', 'id');
    }
}
