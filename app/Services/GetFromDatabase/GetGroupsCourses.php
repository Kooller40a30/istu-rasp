<?php

namespace App\Services\GetFromDatabase;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Репозиторий для получения курсов групп.
 */
class GetGroupsCourses
{
    /**
     * Получает уникальные курсы для указанного факультета.
     *
     * @param int $facultyId Идентификатор факультета.
     * @return Collection|Group[] Коллекция объектов, содержащих поля faculty_id и course_id.
     */
    public static function findCoursesByFaculty(int $facultyId): Collection
    {
        Log::info('[GetGroupsCourses] Action: Finding courses by faculty', ['facultyId' => $facultyId]);
        $result = Group::select('faculty_id', 'course_id')
            ->where('faculty_id', $facultyId)
            ->groupBy('faculty_id', 'course_id')
            ->get();
        Log::info('[GetGroupsCourses] Action: Found courses by faculty', ['count' => $result->count()]);
        return $result;
    }
}
