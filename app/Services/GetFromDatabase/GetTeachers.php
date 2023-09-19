<?php

namespace App\Services\GetFromDatabase;

use App\Models\Faculty;
use App\Models\Teacher;

class GetTeachers{
    public static function teachers($faculty_id = null, $department_id = null){
        if ($faculty_id == null){
            return Teacher::select('teachers.id','nameTeacher')
                ->join('schedules','teachers.id','=','schedules.teacher_id')
                ->groupBy('teachers.id','nameTeacher')
                ->orderBy('nameTeacher')
                ->get();
        } elseif ($department_id == null){
            return Teacher::select('teachers.id','nameTeacher')
                ->join('department_teacher','teachers.id','=','department_teacher.teacher_id')
                ->join('departments','departments.id','=','department_teacher.department_id')
                ->where('departments.faculty_id',$faculty_id)
                ->join('schedules','teachers.id','=','schedules.teacher_id')
                ->groupBy('teachers.id','nameTeacher')
                ->orderBy('nameTeacher')
                ->get();
        } else {
            return Teacher::select('teachers.id','nameTeacher')
                ->join('department_teacher','teachers.id','=','department_teacher.teacher_id')
                ->join('departments','departments.id','=','department_teacher.department_id')
                ->where('departments.id',$department_id)
                ->join('schedules','teachers.id','=','schedules.teacher_id')
                ->groupBy('teachers.id','nameTeacher')
                ->orderBy('nameTeacher')
                ->get();
        }
    }
}
