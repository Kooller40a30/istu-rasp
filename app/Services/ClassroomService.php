<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ClassroomService 
{
    public static function filterClassrooms(Department $department = null, Faculty $faculty = null): Collection
    {
        Log::info('[ClassroomService] Action: Filtering classrooms', ['department_id' => $department?->id, 'faculty_id' => $faculty?->id]);
        $result = Classroom::query()
            ->when($department, function ($query, $department) {
                if ($department->id == Department::NOT_VALID_ID) {
                    return $query->whereNull('department_id');
                }
                return $query->where('department_id', $department->id);
            })
            ->when($faculty, function ($query, $faculty) {
                if ($faculty->id == Faculty::NOT_VALID_ID) {
                    return $query->whereNull('faculty_id');
                }
                return $query->where('faculty_id', $faculty->id);
            })
            ->get();
        Log::info('[ClassroomService] Action: Filtered classrooms', ['count' => $result->count()]);
        return $result;
    }
}