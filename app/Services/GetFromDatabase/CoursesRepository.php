<?php


namespace App\Services\GetFromDatabase;

use App\Models\Course;

class CoursesRepository 
{

    public static function findOne(int $id)
    {
        return Course::where('id', '=', $id)->first();
    }

    public static function findAll(int $faculty = 0) 
    {
        $model = Course::select('course.*')
            ->join('groups', 'groups.course_id', '=', 'course.id')            
            ->groupBy('course.id');            
            if ($faculty) {
                $model->where('groups.faculty_id', $faculty);
            }
        return $model->get();
    }
}