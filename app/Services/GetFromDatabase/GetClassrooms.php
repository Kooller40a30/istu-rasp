<?php

namespace App\Services\GetFromDatabase;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с аудиториями.
 *
 * Предоставляет методы для получения данных об аудиториях из базы данных.
 */
class GetClassrooms
{
    /**
     * Получает список аудиторий с опциональной фильтрацией по факультету и кафедре.
     *
     * @param int $facultyId Идентификатор факультета для фильтрации (0 - без фильтрации).
     * @param int $departmentId Идентификатор кафедры для фильтрации (0 - без фильтрации).
     * @return Collection|Classroom[] Коллекция объектов Classroom.
     */
    public static function findAll(int $facultyId = 0, int $departmentId = 0): Collection
    {
        $query = Classroom::select(
            'classrooms.id',
            'numberClassroom',
            'classrooms.faculty_id',
            'classrooms.department_id'
        );

        if ($facultyId > 0) {
            $query->where('classrooms.faculty_id', $facultyId);
        }

        if ($departmentId > 0) {
            $query->where('classrooms.department_id', $departmentId);
        }

        return $query->join('schedules', 'classrooms.id', '=', 'schedules.classroom_id')
            ->groupBy('classrooms.id', 'numberClassroom')
            ->orderBy('numberClassroom')
            ->get();
    }
}
