<?php

namespace App\Services\GetFromDatabase;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с кафедрами.
 */
class GetDepartments
{
    /**
     * Получает кафедры, где преподают.
     *
     * @param int|null $facultyId Идентификатор факультета, если указан.
     * @return Collection|Department[]
     */
    public static function findTeachersDepartments(?int $facultyId = null): Collection
    {
        $query = Department::select('departments.id', 'nameDepartment', 'departments.faculty_id')
            ->join('department_teacher', 'departments.id', '=', 'department_teacher.department_id')
            ->groupBy('departments.id', 'nameDepartment', 'departments.faculty_id')
            ->orderBy('nameDepartment');

        if ($facultyId !== null) {
            $query->where('departments.faculty_id', $facultyId);
        }

        return $query->get();
    }

    /**
     * Получает кафедры, связанные с аудиториями.
     *
     * @param int|null $facultyId Идентификатор факультета, если указан.
     * @return Collection|Department[] Коллекция кафедр с дополнительной записью, если применимо.
     */
    public static function findClassroomsDepartments(?int $facultyId = null): Collection
    {
        $query = Department::select('departments.id', 'nameDepartment', 'departments.faculty_id')
            ->join('classrooms', 'departments.id', '=', 'classrooms.department_id')
            ->groupBy('departments.id', 'nameDepartment', 'departments.faculty_id')
            ->orderBy('nameDepartment');

        if ($facultyId !== null && $facultyId > 0) {
            $query->where('departments.faculty_id', $facultyId);
        }

        $departments = $query->get();

        if ($facultyId !== null && $facultyId > 0) {
            // Добавляем запись "без кафедры".
            $departments->push((object)[
                'id'             => 0,
                'nameDepartment' => 'без кафедры',
                'faculty_id'     => $facultyId,
            ]);
        }

        return $departments;
    }
}
