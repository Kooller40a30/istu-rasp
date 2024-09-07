<?php

namespace App\Services\GetFromDatabase;

use App\Models\Group;

class GetGroups 
{
    /**
     * Перечень групп
     *
     * @param integer $faculty факультет
     * @param integer $course курс
     * @return array
     */
    public static function groups(int $faculty = 0, int $course = 0)
    {
        $builder = Group::select('id', 'nameGroup')
            ->where(function($query) use ($faculty, $course){
                if ($faculty) {
                    $query->where("faculty_id", $faculty);
                }
                if ($course) {
                    $query->where("course_id", $course);
                }
            });
        return $builder->get();
    }

    /**
     * Перечень курсов
     *
     * @param integer $faculty факультет
     * @return array
     */
    public static function courses(int $faculty) 
    {
        return Group::select('distinct course')
            ->where('faculty_id', $faculty)
            ->orderBy('course')
            ->get();
    }
}
