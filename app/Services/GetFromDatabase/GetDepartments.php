<?php

namespace App\Services\GetFromDatabase;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
        Log::info('[GetDepartments] Action: Finding teachers departments', ['facultyId' => $facultyId]);
        $query = Department::select('departments.id', 'nameDepartment', 'departments.faculty_id')
            ->whereHas('teachers')
            ->orderBy('nameDepartment');

        if ($facultyId) {
            $query->where('departments.faculty_id', $facultyId);
        }

        $result = $query->get();
        Log::info('[GetDepartments] Action: Found teachers departments', ['facultyId' => $facultyId, 'count' => $result->count()]);
        return $result;
    }

    /**
     * Получает кафедры, связанные с аудиториями.
     *
     * @param int|null $facultyId Идентификатор факультета, если указан.
     * @return Collection|Department[] Коллекция кафедр с дополнительной записью, если применимо.
     */
    public static function findClassroomsDepartments(?int $facultyId = null): Collection
    {
        Log::info('[GetDepartments] Action: Finding classrooms departments', ['facultyId' => $facultyId]);
        $query = Department::select('departments.id', 'nameDepartment', 'departments.faculty_id')
            ->whereHas('classrooms')
            ->orderBy('nameDepartment');

        if ($facultyId) {
            $query->where('departments.faculty_id', $facultyId);
        }

        $departments = $query->get();

        if ($facultyId) {
            // Добавляем запись "без кафедры".
            $departments->push((object)[
                'id'             => 0,
                'nameDepartment' => 'без кафедры',
                'faculty_id'     => $facultyId,
            ]);
        }

        Log::info('[GetDepartments] Action: Found classrooms departments', ['facultyId' => $facultyId, 'count' => $departments->count()]);
        return $departments;
    }
}
