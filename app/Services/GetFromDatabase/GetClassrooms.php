<?php

namespace App\Services\GetFromDatabase;

use App\Models\Classroom;
use App\Models\Faculty;
use App\Models\Teacher;

class GetClassrooms{

    public static function classrooms(int $faculty_id = 0, int $department_id = 0)
    {
        $model = Classroom::select('classrooms.id','numberClassroom','classrooms.faculty_id','classrooms.department_id');
        if ($faculty_id > 0) {
            $model->where('faculty_id', $faculty_id);
        }
        if ($department_id > 0) {
            $model->where('department_id', $department_id);
        }
        return $model->join('schedules','classrooms.id','=','schedules.classroom_id')
            ->groupBy('classrooms.id','numberClassroom')
            ->orderBy('numberClassroom')
            ->get();
    }
}
