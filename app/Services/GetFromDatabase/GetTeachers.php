<?php

namespace App\Services\GetFromDatabase;

use App\Models\Teacher;

class GetTeachers 
{
    public static function teachers($faculty_id = null, $department_id = null)
    {
        $teachers = Teacher::select('teachers.id','shortNameTeacher')
            ->join('teacher_schedule','teachers.id','=','teacher_schedule.teacher_id')
            ->join('department_teacher','teachers.id','=','department_teacher.teacher_id')
            ->join('departments','departments.id','=','department_teacher.department_id');
        if ($faculty_id) {
            $teachers = $teachers->where('departments.faculty_id', $faculty_id);
        }
        if ($department_id) {
            $teachers = $teachers->where('departments.id', $department_id);
        }
        return $teachers->groupBy('teachers.id','nameTeacher')
                        ->orderBy('nameTeacher')->get();        
    }
}
