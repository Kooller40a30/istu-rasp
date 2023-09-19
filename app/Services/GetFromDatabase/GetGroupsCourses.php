<?php

namespace App\Services\GetFromDatabase;

use App\Models\Classroom;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Teacher;

class GetGroupsCourses{

    public static function courses($faculty_id)
    {
        return Group::groupBy('faculty_id','course')
            ->having('faculty_id','=',$faculty_id)
            ->select('faculty_id','course')
            ->get();
    }
}
