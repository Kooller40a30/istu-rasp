<?php

namespace App\Services\GetFromDatabase;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;

class GetDepartments{
    public static function teachersDepartments($faculty_id = null){
        if ($faculty_id == null){
            return Department::select('departments.id','nameDepartment','departments.faculty_id')
                ->join('department_teacher','departments.id','=','department_teacher.department_id')
                ->groupBy('departments.id','nameDepartment')
                ->orderBy('nameDepartment')
                ->get();
        } else {
            return Department::select('departments.id','nameDepartment','departments.faculty_id')
                ->where('departments.faculty_id',$faculty_id)
                ->join('department_teacher','departments.id','=','department_teacher.department_id')
                ->groupBy('departments.id','nameDepartment')
                ->orderBy('nameDepartment')
                ->get();
        }
    }
    public static function classroomsDepartments($faculty_id = null){
        if ($faculty_id == null || $faculty_id == 0){
            $departments = Department::select('departments.id','nameDepartment','departments.faculty_id')
                ->join('classrooms','departments.id','=','classrooms.department_id')
                ->groupBy('departments.id','nameDepartment')
                ->orderBy('nameDepartment')
                ->get();
        } else {
            $departments = Department::select('departments.id','nameDepartment','departments.faculty_id')
                ->where('departments.faculty_id',$faculty_id)
                ->join('classrooms','departments.id','=','classrooms.department_id')
                ->groupBy('departments.id','nameDepartment')
                ->orderBy('nameDepartment')
                ->get();
            $departments[count($departments)] = [
                'id' => 0,
                'nameDepartment' => 'без кафедры',
                'faculty_id' => "$faculty_id",
            ];
        }
        return $departments;
    }
}
