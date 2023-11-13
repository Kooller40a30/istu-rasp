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
    public static function groups(int $faculty, int $course)
    {
        $builder = Group::select('id', 'nameGroup')
            ->where("faculty_id", $faculty);
        if ($course > 0) {
            $builder->where("course_id", $course);
        }
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
