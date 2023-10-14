<?php

namespace App\Services\GetFromDatabase;

use App\Models\Classroom;
use App\Models\Faculty;
use App\Models\Teacher;

class GetClassrooms{

    public static function classrooms($faculty_id,$department_id = null)
    {
        $classroomsNew = [[]];
        $i = 0;
        if($faculty_id == null){
            //$classrooms = Classroom::orderBy('numberClassroom')->get();
            return Classroom::select('classrooms.id','numberClassroom','classrooms.faculty_id','classrooms.department_id')
                ->join('schedules','classrooms.id','=','schedules.classroom_id')
                ->groupBy('classrooms.id','numberClassroom')
                ->orderBy('numberClassroom')
                ->get();
         }elseif ($faculty_id == 0) {
            //$classrooms = Classroom::where('faculty_id', null)->orderBy('numberClassroom')->get();
            return Classroom::select('classrooms.id','numberClassroom','classrooms.faculty_id','classrooms.department_id')
                ->where('faculty_id', null)
                ->join('schedules','classrooms.id','=','schedules.classroom_id')
                ->groupBy('classrooms.id','numberClassroom')
                ->orderBy('numberClassroom')
                ->get();
        } elseif ($department_id == 0 && $department_id != null) {
            //$classrooms = Classroom::where('department_id', $department_id)->orderBy('numberClassroom')->get();
            return Classroom::select('classrooms.id','numberClassroom','classrooms.faculty_id','classrooms.department_id')
                ->where('faculty_id', $faculty_id)
                ->where('department_id', null)
                ->join('schedules','classrooms.id','=','schedules.classroom_id')
                ->groupBy('classrooms.id','numberClassroom')
                ->orderBy('numberClassroom')
                ->get();
        } elseif ($department_id != null) {
        //$classrooms = Classroom::where('department_id', $department_id)->orderBy('numberClassroom')->get();
            return Classroom::select('classrooms.id','numberClassroom','classrooms.faculty_id','classrooms.department_id')
                ->where('faculty_id', $faculty_id)
                ->where('department_id', $department_id)
                ->join('schedules','classrooms.id','=','schedules.classroom_id')
                ->groupBy('classrooms.id','numberClassroom')
                ->orderBy('numberClassroom')
                ->get();
        } else {
            //$classrooms = Classroom::where('faculty_id', $faculty_id)->orderBy('numberClassroom')->get();
            return Classroom::select('classrooms.id','numberClassroom','classrooms.faculty_id','classrooms.department_id')
                ->where('faculty_id', $faculty_id)
                ->join('schedules','classrooms.id','=','schedules.classroom_id')
                ->groupBy('classrooms.id','numberClassroom')
                ->orderBy('numberClassroom')
                ->get();
        }
    }
}
