<?php


namespace App\Services\GetFromDatabase;

use App\Models\Course;

class CoursesRepository 
{
    public static function findAll(int $faculty = 0) 
    {
        return Course::select('course.*')
            ->join('groups', 'groups.course_id', '=', 'course.id')
            ->where('groups.faculty_id', $faculty)
            ->groupBy('course.id')
            ->get();
    }
}