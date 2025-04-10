<?php

namespace App\Services\GetFromDatabase;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
        Log::info('[GetGroups] Action: Finding groups', ['faculty' => $faculty, 'course' => $course]);
        $result = Group::select('id', 'nameGroup')
            ->when($faculty, function ($query, $faculty) {
                $query->where('faculty_id', $faculty);
            })
            ->when($course, function ($query, $course) {
                $query->where('course_id', $course);
            })
            ->get();
        Log::info('[GetGroups] Action: Found groups', ['count' => $result->count()]);
        return $result;
    }

    /**
     * Получает перечень курсов для указанного факультета.
     *
     * @param int $faculty Идентификатор факультета.
     * @return Collection|Group[] Коллекция объектов с уникальными значениями курса.
     */
    public static function findCourses(int $faculty): Collection
    {
        Log::info('[GetGroups] Action: Finding courses for faculty', ['faculty' => $faculty]);
        $result = Group::select('course')
            ->where('faculty_id', $faculty)
            ->distinct()
            ->orderBy('course')
            ->get();
        Log::info('[GetGroups] Action: Found courses for faculty', ['count' => $result->count()]);
        return $result;
    }
}
