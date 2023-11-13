<?php

namespace App\Services\GetFromDatabase;

use App\Models\Group;

class GetGroupsCourses{

    public static function courses($faculty_id)
    {
        return Group::groupBy('faculty_id','course_id')
            ->having('faculty_id','=',$faculty_id)
            ->select('faculty_id','course_id')
            ->get();
    }
}
