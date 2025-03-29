<?php

namespace App\Services\GetFromDatabase;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с преподавателями.
 */
class GetTeachers
{
    /**
     * Получает список преподавателей с опциональной фильтрацией по факультету и кафедре.
     *
     * @param int|null $facultyId Идентификатор факультета.
     * @param int|null $departmentId Идентификатор кафедры.
     * @return Collection|Teacher[]
     */
    public static function findTeachers(?int $facultyId = null, ?int $departmentId = null): Collection
    {
        return Teacher::select('teachers.id', 'shortNameTeacher')
            ->join('teacher_schedule', 'teachers.id', '=', 'teacher_schedule.teacher_id')
            ->join('department_teacher', 'teachers.id', '=', 'department_teacher.teacher_id')
            ->join('departments', 'departments.id', '=', 'department_teacher.department_id')
            ->when($facultyId, function ($query, $facultyId) {
                $query->where('departments.faculty_id', $facultyId);
            })
            ->when($departmentId, function ($query, $departmentId) {
                $query->where('departments.id', $departmentId);
            })
            ->groupBy('teachers.id', 'shortNameTeacher')
            ->orderBy('shortNameTeacher')
            ->get();
    }
}
