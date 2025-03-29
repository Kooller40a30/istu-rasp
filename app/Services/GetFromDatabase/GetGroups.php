<?php

namespace App\Services\GetFromDatabase;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с группами.
 */
class GetGroups
{
    /**
     * Получает перечень групп с опциональной фильтрацией по факультету и курсу.
     *
     * @param int $faculty Идентификатор факультета (0 - без фильтрации).
     * @param int $course  Номер курса (0 - без фильтрации).
     * @return Collection|Group[] Коллекция объектов Group.
     */
    public static function findGroups(int $faculty = 0, int $course = 0): Collection
    {
        return Group::select('id', 'nameGroup')
            ->when($faculty, function ($query, $faculty) {
                $query->where('faculty_id', $faculty);
            })
            ->when($course, function ($query, $course) {
                $query->where('course_id', $course);
            })
            ->get();
    }

    /**
     * Получает перечень курсов для указанного факультета.
     *
     * @param int $faculty Идентификатор факультета.
     * @return Collection|Group[] Коллекция объектов с уникальными значениями курса.
     */
    public static function findCourses(int $faculty): Collection
    {
        return Group::select('course')
            ->where('faculty_id', $faculty)
            ->distinct()
            ->orderBy('course')
            ->get();
    }
}
