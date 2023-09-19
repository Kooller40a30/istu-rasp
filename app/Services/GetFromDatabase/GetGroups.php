<?php

namespace App\Services\GetFromDatabase;

use App\Models\Classroom;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Teacher;

class GetGroups{

    public static function groups($faculty_id,$course = null)
    {
        if ($course != null){
            $groups = Group::where('faculty_id', $faculty_id)
                ->where('course', $course)
                ->get();
        } else {
            $groups = Group::where('faculty_id', $faculty_id)
                ->get();
        }
        return $groups;
    }
}
