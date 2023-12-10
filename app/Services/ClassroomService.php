<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;

class ClassroomService 
{
    public static function filterClassrooms(Department $department = null, Faculty $faculty = null)
    {
        $query = Classroom::query();
        
        if ($department && $department->id == Department::NOT_VALID_ID) {
            $query->whereNull('department_id');
        } elseif ($department) {
            $query->where('department_id', $department->id);
        }

        if ($faculty && $faculty->id == Faculty::NOT_VALID_ID) {
            $query->whereNull('faculty_id');
        } elseif ($faculty) {
            $query->where('faculty_id', $faculty->id);
        }

        $classrooms = $query->get();

        return $classrooms;
    }
}