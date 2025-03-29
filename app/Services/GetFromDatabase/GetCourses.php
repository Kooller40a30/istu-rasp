<?php

namespace App\Services\GetFromDatabase;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с курсами.
 *
 * Предоставляет методы для получения данных о курсах из базы данных.
 */
class GetCourses
{
    /**
     * Получает курс по его идентификатору.
     *
     * @param int $id Идентификатор курса.
     * @return Course|null Возвращает объект Course, если найден, или null.
     */
    public static function findById(int $id): ?Course
    {
        return Course::find($id);
    }

    /**
     * Получает список курсов с опциональной фильтрацией по факультету.
     *
     * @param int $facultyId Идентификатор факультета для фильтрации. Если равен 0, фильтрация не применяется.
     * @return Collection|Course[] Коллекция объектов Course.
     */
    public static function findAll(int $facultyId = 0): Collection
    {
        $query = Course::select('course.*')
            ->join('groups', 'groups.course_id', '=', 'course.id')
            ->groupBy('course.id');

        if ($facultyId > 0) {
            $query->where('groups.faculty_id', $facultyId);
        }

        return $query->get();
    }
}
